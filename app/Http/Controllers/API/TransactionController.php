<?php

namespace App\Http\Controllers\API;

use App\Enums\TransactionFulfillmentType;
use App\Enums\TransactionPickupStatus;
use App\Enums\TransactionShipmentStatus;
use App\Enums\TransactionStatus;
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

    public function show(string $transactionId)
    {
        try {
            $transaction = Transaction::query()
                ->with(['items', 'shipment', 'pickup'])
                ->where('id', $transactionId)
                ->first();

            return ApiResponse::success([
                'data' => new TransactionResource($transaction),
            ], 'Transaction retrieved successfully');
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

            /**
             * ======================================
             * 1. VALIDASI ITEM & CEK STOK OXY
             * ======================================
             */
            foreach ($validated['items'] as $item) {

                $response = ItemMasterOxyService::getItemMasterDetail(
                    token: $oxyAccessToken,
                    itemMasterId: $item['oxyItemMasterId'],
                    locationId: $validated['oxyLocationId'] ?? null,
                    page: 0,
                    size: 1
                );

                if (
                    ($response['success'] ?? false) !== true ||
                    empty($response['data']['data'][0])
                ) {
                    throw new \Exception(
                        'Item master not found: ' . $item['oxyItemMasterId']
                    );
                }

                $itemMaster = $response['data']['data'][0];

                $stock = $itemMaster['stocks'][0]['qty'] ?? 0;
                if ($stock < $item['qty']) {
                    throw new \Exception(
                        'Stock not enough: ' . $item['oxyItemMasterId']
                    );
                }

                $price = $itemMaster['prices'][0]['sellingPrice'];
                $subtotal += $price * $item['qty'];

                $itemMasters[] = $itemMaster;
            }

            /**
             * ======================================
             * 2. CREATE TRANSACTION
             * ======================================
             */
            $transaction = Transaction::create([
                'id' => (string) Str::ulid(),
                'oxy_customer_id' => $validated['oxyCustomerId'],
                'oxy_location_id' => $validated['oxyLocationId'],
                'status' => TransactionStatus::PENDING,
                'fulfillment_type' => $validated['fulfillmentType'],
                'subtotal' => $subtotal,
                'shipping_cost' => 0,
                'total' => $subtotal,
            ]);

            /**
             * ======================================
             * 3. CREATE TRANSACTION ITEMS
             * ======================================
             */
            $itemsByMasterId = collect($validated['items'])
                ->keyBy('oxyItemMasterId');

            foreach ($itemMasters as $itemMaster) {

                $itemMasterId = $itemMaster['itemMasterId'];
                $qty = $itemsByMasterId[$itemMasterId]['qty'];
                $price = $itemMaster['prices'][0]['sellingPrice'];

                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'oxy_item_master_id' => $itemMasterId,
                    'quantity' => $qty,
                    'price' => $price,
                    'subtotal' => $price * $qty,
                ]);
            }

            /**
             * ======================================
             * 4. SHIPMENT
             * ======================================
             */
            if ($validated['fulfillmentType'] === TransactionFulfillmentType::SHIPMENT->value) {

                // sementara dummy (API Grab async nanti)
                $shipmentPayload = [
                    'grab_delivery_id' => (string) Str::ulid(),
                    'grab_shipping_cost' => 0,
                    'status' => TransactionShipmentStatus::PENDING,
                    'receiver_name' => $validated['receiverName'],
                    'receiver_phone_number' => $validated['receiverPhoneNumber'],
                    'receiver_address' => $validated['shipmentAddress'],
                    'grab_json_response' => json_encode([
                        'note' => 'Grab integration pending'
                    ]),
                ];

                $transaction->shipment()->create($shipmentPayload);

                // update total jika ada ongkir
                $transaction->update([
                    'shipping_cost' => $shipmentPayload['grab_shipping_cost'],
                    'total' => $transaction->subtotal + $shipmentPayload['grab_shipping_cost'],
                ]);
            }

            /**
             * ======================================
             * 5. PICKUP
             * ======================================
             */
            if ($validated['fulfillmentType'] === TransactionFulfillmentType::PICKUP->value) {

                $transaction->pickup()->create([
                    'pickup_code' => (string) Str::ulid(),
                    'pickup_time' => $validated['pickupTime'],
                    'pickup_end_time' => $validated['pickupEndTime'] ?? null,
                    'receiver_name' => $validated['receiverName'],
                    'receiver_phone_number' => $validated['receiverPhoneNumber'],
                    'status' => TransactionPickupStatus::PENDING,
                ]);
            }

            DB::commit();

            return ApiResponse::success(
                data: $transaction->id,
                message: 'Transaction created successfully'
            );
        } catch (\Throwable $e) {
            DB::rollBack();

            return ApiResponse::error([
                'detail' => $e->getMessage(),
            ], 'Failed to create transaction');
        }
    }
}
