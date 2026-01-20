<?php

namespace App\Http\Controllers\API;

use App\Enums\TransactionFulfillmentType;
use App\Enums\TransactionPickupStatus;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\Transaction\CreateTransactionRequest;
use App\Http\Resources\PaginationResource;
use App\Http\Resources\Transaction\TransactionResource;
use App\Models\OxyApiToken;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Services\External\Oxy\ItemMasterOxyService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    public function index(Request $request, string $oxyCustomerId)
    {
        try {
            $page = $request->page ?? 1;
            $size = $request->size ?? 10;
            $transactionId = $request->transactionId ?? null;

            if (!$oxyCustomerId) {
                return ApiResponse::error(
                    data: null,
                    message: 'Oxy customer id is required',
                    statusCode: Response::HTTP_UNAUTHORIZED
                );
            }

            $transactions = Transaction::query()
                ->with(['items', 'shipment', 'pickup'])
                ->where('oxy_customer_id', $oxyCustomerId)
                ->when(
                    $transactionId,
                    fn($q) => $q->where('id', 'like', '%' . $transactionId . '%')
                )
                ->paginate(
                    $size,
                    ['*'],
                    'page',
                    $page
                )->appends([
                    'page' => $page,
                    'size' => $size
                ]);

            return ApiResponse::success([
                'data' => TransactionResource::collection($transactions),
                'pagination' => new PaginationResource($transactions),
            ], 'Transactions retrieved successfully');
        } catch (\Throwable $e) {
            return ApiResponse::error([
                'detail' => $e->getMessage(),
            ]);
        }
    }

    public function store(CreateTransactionRequest $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();

            $oxyAccessToken = OxyApiToken::getAccessToken();

            $subtotal = 0;
            $itemMasters = [];

            // check stock items master in oxy
            foreach ($validated['items'] as $item) {
                $response = ItemMasterOxyService::getItemMasterDetail(
                    token: $oxyAccessToken,
                    itemMasterId: $item->oxyItemMasterId,
                    locationId: $validated['oxyLocationId'] ?? null,
                    page: 0,
                    size: 1
                );

                // if item master not found
                if (
                    ($response['success'] ?? false) !== true ||
                    !isset($response['data']['data']) ||
                    empty($response['data']['data']) ||
                    !isset($response['data']['data'][0])
                ) {
                    return ApiResponse::error(
                        data: [
                            'detail' => 'Item master not found' . ' oxyItemMasterId: ' . $item->oxyItemMasterId,
                        ],
                        message: 'Item master not found' . ' oxyItemMasterId: ' . $item->oxyItemMasterId,
                        statusCode: Response::HTTP_NOT_FOUND
                    );
                }

                // if stock not enough
                if (
                    ($response['data']['data'][0]['stocks'][0]['qty'] ?? 0) < $item->quantity
                ) {
                    return ApiResponse::error(
                        data: [
                            'detail' => 'Stock not enough' . ' oxyItemMasterId: ' . $item->oxyItemMasterId,
                        ],
                        message: 'Stock not enough' . ' oxyItemMasterId: ' . $item->oxyItemMasterId,
                        statusCode: Response::HTTP_CONFLICT
                    );
                }

                $subtotal += $response['data']['data'][0]['prices'][0]['sellingPrice'] * $item->quantity;
                $itemMasters[] = $response['data']['data'][0];
            }

            // check ongkir to grab
            $grabResponse = [
                'grab_delivery_id' => (string) Str::ulid(),
                'grab_shipping_cost' => 0,
                'status' => 'PENDING',
                'receiver_name' => $validated['receiverName'],
                'receiver_phone_number' => $validated['receiverPhoneNumber'],
                'receiver_address' => $validated['shipmentAddress'],
                'shipment_latitude' => $validated['shipmentLatitude'],
                'shipment_longitude' => $validated['shipmentLongitude'],
            ];

            // create transaction
            $transactionId = (string) Str::ulid();

            $transaction = Transaction::create([
                'id' => $transactionId,
                'oxy_customer_id' => $validated['oxyCustomerId'],
                'oxy_location_id' => $validated['oxyLocationId'],
                'status' => 'PENDING',
                'fulfillment_type' => $validated['fulfillmentType'],
                'subtotal' => $subtotal,
                'shipping_cost' => $grabResponse['grab_shipping_cost'],
                'total' => $subtotal + $grabResponse['grab_shipping_cost'],
            ]);

            $items = $validated['items'];

            // create transaction items
            foreach ($itemMasters as $item) {

                $itemMasterId = $item->itemMasterId;

                // ambil qty dari request
                $qty = $items[$itemMasterId]['qty'];

                $price = $item['prices'][0]['sellingPrice'];
                $subtotal = $price * $qty;

                TransactionItem::create([
                    'transaction_id' => $transactionId,
                    'oxy_item_master_id' => $itemMasterId,
                    'quantity' => $qty,
                    'price' => $price,
                    'subtotal' => $subtotal,
                ]);

                // update stock item master oxy
                // integrasi ke api oxy
            }

            // if SHIPMENT create transaction shipment
            if ($validated['fulfillmentType'] === TransactionFulfillmentType::SHIPMENT->value) {
                $transaction->shipment()->create([
                    'transaction_id' => $transactionId,
                    'grab_delivery_id' => $grabResponse['grab_delivery_id'],
                    'grab_shipping_cost' => $grabResponse['grab_shipping_cost'],
                    'status' => $grabResponse['status'],
                    'receiver_name' => $grabResponse['receiver_name'],
                    'receiver_phone_number' => $grabResponse['receiver_phone_number'],
                    'receiver_address' => $grabResponse['receiver_address'],
                    'grab_json_response' => json_encode($grabResponse),
                ]);
            }

            // if PICKUP create transaction pickup
            if ($validated['fulfillmentType'] === TransactionFulfillmentType::PICKUP->value) {
                $transaction->pickup()->create([
                    'transaction_id' => $transactionId,
                    'pickup_code' => (string) Str::ulid(),
                    'pickup_time' => $validated['pickupTime'],
                    'receiver_name' => $validated['receiverName'],
                    'receiver_phone_number' => $validated['receiverPhoneNumber'],
                    'status' => TransactionPickupStatus::PENDING->value,
                ]);
            }

            DB::commit();

            return ApiResponse::success(
                data: $transactionId,
                message: 'Transaction created successfully'
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return ApiResponse::error(
                [
                    'detail' => $e->getMessage(),
                ]
            );
        }
    }
}
