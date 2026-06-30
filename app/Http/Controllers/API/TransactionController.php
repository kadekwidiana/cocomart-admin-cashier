<?php

namespace App\Http\Controllers\API;

use App\Enums\TransactionFulfillmentType;
use App\Enums\TransactionPickupStatus;
use App\Enums\TransactionShipmentStatus;
use App\Enums\TransactionStatus;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\Transaction\CreateTransactionRequest;
use App\Http\Requests\API\Transaction\PayTransactionRequest;
use App\Http\Resources\PaginationResource;
use App\Http\Resources\Transaction\TransactionResource;
use App\Models\GrabApiToken;
use App\Models\OxyApiToken;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Services\External\Grab\GrabDeliveryPayloadBuilder;
use App\Services\External\Grab\GrabDeliveryPayloadValidator;
use App\Services\External\Grab\GrabDeliveryService;
use App\Services\External\Oxy\ItemMasterOxyService;
use App\Services\External\Oxy\LocationOxyService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
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

    public function showSimple(string $transactionId)
    {
        try {
            $transaction = Transaction::query()
                ->with(['items', 'shipment', 'pickup'])
                ->where('id', $transactionId)
                ->first();

            return ApiResponse::success(
                new TransactionResource($transaction),
                'Transaction retrieved successfully'
            );
        } catch (\Throwable $e) {
            return ApiResponse::error([
                'detail' => $e->getMessage(),
            ]);
        }
    }

    public function showDetail(string $transactionId)
    {
        try {
            $transaction = Transaction::query()
                ->with(['items', 'shipment', 'pickup'])
                ->where('id', $transactionId)
                ->firstOrFail();

            $oxyAccessToken = OxyApiToken::getAccessToken();

            /**
             * HANDLE LOCATION API
             */
            try {
                $oxyLocationRes = LocationOxyService::getLocations(
                    token: $oxyAccessToken,
                );

                $transaction->setAttribute(
                    'location',
                    $oxyLocationRes['data']['data'][0] ?? null
                );
            } catch (\Throwable $e) {
                // jika gagal, tetap lanjut
                $transaction->setAttribute('location', null);
            }

            /**
             * HANDLE ITEM DETAIL API PER ITEM
             */
            foreach ($transaction->items as $item) {

                try {

                    $oxyItemDetail = ItemMasterOxyService::getItemMasterDetail(
                        token: $oxyAccessToken,
                        itemMasterId: $item->oxy_item_master_id,
                        locationId: $transaction->oxy_location_id
                    );

                    $detail = $oxyItemDetail['data']['data'][0] ?? null;

                    $item->setAttribute('oxy_category_id', $detail['category']['categoryId'] ?? null);
                    $item->setAttribute('oxy_sub_category_id', $detail['subCategory']['subCategoryId'] ?? null);
                    $item->setAttribute('oxy_code', $detail['code'] ?? null);
                    $item->setAttribute('oxy_barcode', $detail['barcode'] ?? null);
                    $item->setAttribute('oxy_name', $detail['name'] ?? null);
                } catch (\Throwable $e) {

                    // jika API item gagal, tetap isi null
                    $item->setAttribute('oxy_category_id', null);
                    $item->setAttribute('oxy_sub_category_id', null);
                    $item->setAttribute('oxy_code', null);
                    $item->setAttribute('oxy_barcode', null);
                    $item->setAttribute('oxy_name', null);
                }
            }

            return ApiResponse::success(
                new TransactionResource($transaction),
                'Transaction retrieved successfully'
            );
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error([
                'detail' => 'Transaction not found',
            ], 404);
        } catch (\Throwable $e) {
            return ApiResponse::error([
                'detail' => $e->getMessage(),
            ], 500);
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

                $transaction->shipment()->create([
                    'grab_delivery_id' => null,
                    'grab_shipping_cost' => 0,
                    'grab_vehicle_type' => null,
                    'grab_service_type' => null,
                    'status' => TransactionShipmentStatus::PENDING,
                    'receiver_name' => $validated['receiverName'],
                    'receiver_phone_number' => $validated['receiverPhoneNumber'],
                    'receiver_address' => $validated['shipmentAddress'],
                    'receiver_latitude' => $validated['shipmentLatitude'],
                    'receiver_longitude' => $validated['shipmentLongitude'],
                    'grab_json_response' => null,
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

    public function pay(PayTransactionRequest $request, string $transactionId)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validated();

            $transaction = Transaction::query()
                ->with(['items', 'shipment'])
                ->where('id', $transactionId)
                ->firstOrFail();

            if ($transaction->status !== TransactionStatus::PENDING) {
                return ApiResponse::error(
                    data: null,
                    message: 'Transaction is not payable (status must be PENDING)',
                    statusCode: Response::HTTP_CONFLICT
                );
            }

            if ($transaction->fulfillment_type !== TransactionFulfillmentType::SHIPMENT) {
                return ApiResponse::error(
                    data: null,
                    message: 'Only SHIPMENT transactions can be paid through this endpoint',
                    statusCode: Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $shipment = $transaction->shipment;

            if (!$shipment) {
                return ApiResponse::error(
                    data: null,
                    message: 'Shipment data not found for this transaction',
                    statusCode: Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $transaction->update(['status' => TransactionStatus::PAID]);

            $vehicleType = $validated['vehicleType'];
            $shipment->grab_vehicle_type = $vehicleType;
            $shipment->grab_service_type = config('services.grab.default_service_type');

            $grabToken = GrabApiToken::getValidAccessToken();

            if (!$grabToken) {
                DB::rollBack();

                return ApiResponse::error(
                    data: null,
                    message: 'Grab access token not available',
                    statusCode: Response::HTTP_SERVICE_UNAVAILABLE
                );
            }

            $oxyAccessToken = OxyApiToken::getAccessToken();

            $quotePayload = GrabDeliveryPayloadBuilder::build(
                transaction: $transaction,
                oxyAccessToken: $oxyAccessToken,
                vehicleType: $vehicleType
            );

            $deliveryPayload = GrabDeliveryPayloadBuilder::withDeliveryDetails(
                quotePayload: $quotePayload,
                transaction: $transaction,
                oxyAccessToken: $oxyAccessToken
            );

            $payloadErrors = GrabDeliveryPayloadValidator::validate($deliveryPayload);

            if (!empty($payloadErrors)) {
                DB::rollBack();

                return ApiResponse::error(
                    data: $payloadErrors,
                    message: 'Invalid Grab delivery payload',
                    statusCode: Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $createRes = GrabDeliveryService::createDelivery($grabToken, $deliveryPayload);

            if (!($createRes['success'] ?? false)) {
                DB::rollBack();

                return ApiResponse::error(
                    data: $createRes['error'] ?? null,
                    message: $createRes['message'] ?? 'Grab create delivery failed',
                    statusCode: $createRes['code'] ?? Response::HTTP_BAD_GATEWAY
                )->header('X-Error-Source', 'grab');
            }

            $shippingCost = $createRes['data']['quote']['amount'] ?? 0;

            $shipment->grab_delivery_id = $createRes['data']['deliveryID'] ?? null;
            $shipment->grab_shipping_cost = $shippingCost;
            $shipment->status = TransactionShipmentStatus::PENDING;
            $shipment->grab_json_response = json_encode($createRes['data']);
            $shipment->save();

            $transaction->update([
                'shipping_cost' => $shippingCost,
                'total' => $transaction->subtotal + $shippingCost,
            ]);

            DB::commit();

            $transaction->refresh()->load(['items', 'shipment', 'pickup']);

            return ApiResponse::success(
                data: new TransactionResource($transaction),
                message: 'Transaction paid successfully'
            );
        } catch (ModelNotFoundException $e) {
            DB::rollBack();

            return ApiResponse::error([
                'detail' => 'Transaction not found',
            ], 404);
        } catch (\Throwable $e) {
            DB::rollBack();

            return ApiResponse::error([
                'detail' => $e->getMessage(),
            ], 'Failed to pay transaction');
        }
    }
}
