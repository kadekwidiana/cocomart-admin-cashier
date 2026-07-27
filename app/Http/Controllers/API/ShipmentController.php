<?php

namespace App\Http\Controllers\API;

use App\Enums\TransactionFulfillmentType;
use App\Enums\TransactionShipmentStatus;
use App\Enums\TransactionStatus;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\Shipment\QuoteShipmentRequest;
use App\Http\Requests\API\Shipment\UpdateShipmentReceiverRequest;
use App\Http\Resources\Transaction\TransactionShipmentResource;
use App\Models\GrabApiToken;
use App\Models\OxyApiToken;
use App\Models\Transaction;
use App\Models\TransactionShipment;
use App\Services\External\Grab\GrabDeliveryPayloadBuilder;
use App\Services\External\Grab\GrabDeliveryService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ShipmentController extends Controller
{
    public function quote(QuoteShipmentRequest $request)
    {
        try {
            $validated = $request->validated();

            $transaction = Transaction::query()
                ->with(['items', 'shipment'])
                ->where('id', $validated['transactionId'])
                ->firstOrFail();

            if ($transaction->fulfillment_type !== TransactionFulfillmentType::SHIPMENT) {
                return ApiResponse::error(
                    data: null,
                    message: 'Transaction is not a shipment',
                    statusCode: Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            if (! $transaction->shipment) {
                return ApiResponse::error(
                    data: null,
                    message: 'Shipment data not found for this transaction',
                    statusCode: Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            if ($transaction->payment_token) {
                return ApiResponse::error(
                    data: null,
                    message: 'Metode pembayaran sudah dipilih, ongkir tidak bisa diubah lagi',
                    statusCode: Response::HTTP_CONFLICT
                );
            }

            $token = GrabApiToken::getValidAccessToken();

            if (! $token) {
                return ApiResponse::error(
                    data: null,
                    message: 'Grab access token not available',
                    statusCode: Response::HTTP_SERVICE_UNAVAILABLE
                );
            }

            $oxyAccessToken = OxyApiToken::getAccessToken();

            $payload = GrabDeliveryPayloadBuilder::build(
                transaction: $transaction,
                oxyAccessToken: $oxyAccessToken,
                vehicleType: $validated['vehicleType']
            );

            $response = GrabDeliveryService::quote($token, $payload);

            if (! ($response['success'] ?? false)) {
                return ApiResponse::error(
                    data: $response['error'] ?? null,
                    message: $response['message'] ?? 'Failed to get delivery quote',
                    statusCode: $response['code'] ?? Response::HTTP_BAD_GATEWAY
                );
            }

            $shippingCost = $response['data']['quotes'][0]['amount'] ?? 0;

            DB::transaction(function () use ($transaction, $validated, $shippingCost) {
                $transaction->shipment->update([
                    'grab_vehicle_type' => $validated['vehicleType'],
                    'grab_service_type' => config('services.grab.default_service_type'),
                    'grab_shipping_cost' => $shippingCost,
                ]);

                $transaction->update([
                    'shipping_cost' => $shippingCost,
                    'total' => $transaction->subtotal + $shippingCost,
                ]);
            });

            return ApiResponse::success(
                data: $response['data'],
                message: 'Delivery quote retrieved successfully'
            );
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error([
                'detail' => 'Transaction not found',
            ], 404);
        } catch (\Throwable $e) {
            return ApiResponse::error([
                'detail' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Cek status / tracking pengiriman sebuah transaksi.
     */
    public function show(string $transactionId)
    {
        try {
            $transaction = Transaction::query()
                ->with(['shipment'])
                ->where('id', $transactionId)
                ->firstOrFail();

            $shipment = $this->resolveShipmentWithDelivery($transaction);

            if ($shipment instanceof JsonResponse) {
                return $shipment;
            }

            $token = GrabApiToken::getValidAccessToken();

            if (! $token) {
                return ApiResponse::error(
                    data: null,
                    message: 'Grab access token not available',
                    statusCode: Response::HTTP_SERVICE_UNAVAILABLE
                );
            }

            $response = GrabDeliveryService::getDelivery($token, $shipment->grab_delivery_id);

            if (! ($response['success'] ?? false)) {
                return ApiResponse::error(
                    data: $response['error'] ?? null,
                    message: $response['message'] ?? 'Failed to get delivery',
                    statusCode: $response['code'] ?? Response::HTTP_BAD_GATEWAY
                );
            }

            // Sinkron status terbaru dari Grab ke DB lokal.
            $mappedStatus = $this->mapGrabStatus((string) ($response['data']['status'] ?? ''));

            if ($mappedStatus) {
                $shipment->status = $mappedStatus;
            }
            $shipment->grab_json_response = json_encode($response['data']);
            $shipment->save();

            if ($mappedStatus === TransactionShipmentStatus::DELIVERED) {
                $transaction->update(['status' => TransactionStatus::COMPLETED]);
            }

            return ApiResponse::success(
                data: $response['data'],
                message: 'Delivery retrieved successfully'
            );
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error([
                'detail' => 'Transaction not found',
            ], 404);
        } catch (\Throwable $e) {
            return ApiResponse::error([
                'detail' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Batalkan pengiriman sebuah transaksi (Sistem & Grab).
     */
    public function cancel(string $transactionId)
    {
        try {
            $transaction = Transaction::query()
                ->with(['shipment'])
                ->where('id', $transactionId)
                ->firstOrFail();

            $shipment = $this->resolveShipmentWithDelivery($transaction);

            if ($shipment instanceof JsonResponse) {
                return $shipment;
            }

            $token = GrabApiToken::getValidAccessToken();

            if (! $token) {
                return ApiResponse::error(
                    data: null,
                    message: 'Grab access token not available',
                    statusCode: Response::HTTP_SERVICE_UNAVAILABLE
                );
            }

            $response = GrabDeliveryService::cancelDelivery($token, $shipment->grab_delivery_id);

            if (! ($response['success'] ?? false)) {
                return ApiResponse::error(
                    data: $response['error'] ?? null,
                    message: $response['message'] ?? 'Failed to cancel delivery',
                    statusCode: $response['code'] ?? Response::HTTP_BAD_GATEWAY
                );
            }

            DB::transaction(function () use ($shipment, $transaction, $response) {
                $shipment->status = TransactionShipmentStatus::CANCELED;
                $shipment->grab_json_response = json_encode($response['data']);
                $shipment->save();

                $transaction->update(['status' => TransactionStatus::CANCELED]);
            });

            return ApiResponse::success(
                data: $response['data'],
                message: 'Delivery canceled successfully'
            );
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error([
                'detail' => 'Transaction not found',
            ], 404);
        } catch (\Throwable $e) {
            return ApiResponse::error([
                'detail' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Ubah alamat/koordinat & nomor telepon penerima.
     */
    public function updateReceiver(UpdateShipmentReceiverRequest $request, string $transactionId)
    {
        try {
            $validated = $request->validated();

            $transaction = Transaction::query()
                ->with(['shipment', 'pickup'])
                ->where('id', $transactionId)
                ->firstOrFail();

            if ($transaction->shipment && $transaction->shipment->grab_delivery_id) {
                return ApiResponse::error(
                    data: null,
                    message: 'Delivery sudah dibuat, alamat & telepon tidak bisa diubah',
                    statusCode: Response::HTTP_CONFLICT
                );
            }

            $shipment = DB::transaction(function () use ($transaction, $validated) {
                if ($transaction->fulfillment_type !== TransactionFulfillmentType::SHIPMENT) {
                    $transaction->update([
                        'fulfillment_type' => TransactionFulfillmentType::SHIPMENT,
                    ]);
                }

                $receiverName = $validated['receiverName']
                    ?? $transaction->shipment?->receiver_name
                    ?? $transaction->pickup?->receiver_name;

                $shipment = $transaction->shipment()->updateOrCreate(
                    ['transaction_id' => $transaction->id],
                    [
                        'receiver_name' => $receiverName,
                        'receiver_phone_number' => $validated['receiverPhoneNumber'],
                        'receiver_address' => $validated['shipmentAddress'],
                        'receiver_latitude' => $validated['shipmentLatitude'],
                        'receiver_longitude' => $validated['shipmentLongitude'],
                        'status' => $transaction->shipment?->status ?? TransactionShipmentStatus::PENDING,
                    ]
                );

                $transaction->pickup()->delete();

                return $shipment;
            });

            return ApiResponse::success(
                data: new TransactionShipmentResource($shipment),
                message: 'Receiver updated successfully'
            );
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error([
                'detail' => 'Transaction not found',
            ], 404);
        } catch (\Throwable $e) {
            return ApiResponse::error([
                'detail' => $e->getMessage(),
            ]);
        }
    }

    private function resolveShipmentWithDelivery(Transaction $transaction)
    {
        if ($transaction->fulfillment_type !== TransactionFulfillmentType::SHIPMENT) {
            return ApiResponse::error(
                data: null,
                message: 'Transaction is not a shipment',
                statusCode: Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $shipment = $transaction->shipment;

        if (! $shipment) {
            return ApiResponse::error(
                data: null,
                message: 'Shipment data not found for this transaction',
                statusCode: Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        if (! $shipment->grab_delivery_id) {
            return ApiResponse::error(
                data: null,
                message: 'Delivery belum dibuat untuk transaksi ini (transaksi belum dibayar)',
                statusCode: Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        return $shipment;
    }

    /**
     * Webhook penerima update status delivery dari Grab.
     */
    public function webhook(Request $request)
    {
        try {
            $deliveryId = $request->input('deliveryID')
                ?? $request->input('deliveryId');
            $grabStatus = $request->input('status');

            if (! $deliveryId || ! $grabStatus) {
                return ApiResponse::error(
                    data: null,
                    message: 'Invalid webhook payload',
                    statusCode: Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $shipment = TransactionShipment::query()
                ->where('grab_delivery_id', $deliveryId)
                ->first();

            if (! $shipment) {
                Log::warning('[GRAB] Webhook for unknown delivery id: '.$deliveryId);

                return ApiResponse::error(
                    data: null,
                    message: 'Shipment not found',
                    statusCode: Response::HTTP_NOT_FOUND
                );
            }

            $mappedStatus = $this->mapGrabStatus($grabStatus);

            if ($mappedStatus) {
                $shipment->status = $mappedStatus;
            }
            $shipment->grab_json_response = json_encode($request->all());
            $shipment->save();

            if ($mappedStatus === TransactionShipmentStatus::DELIVERED) {
                Transaction::query()
                    ->where('id', $shipment->transaction_id)
                    ->update(['status' => TransactionStatus::COMPLETED]);
            }

            return ApiResponse::success(
                data: null,
                message: 'Webhook processed'
            );
        } catch (\Throwable $e) {
            Log::error('[GRAB] Webhook error: '.$e->getMessage());

            return ApiResponse::error([
                'detail' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Petakan status delivery Grab ke TransactionShipmentStatus internal.
     * Nilai status mengikuti dokumentasi resmi GrabExpress:
     * ALLOCATING -> PENDING_PICKUP -> PICKING_UP -> PENDING_DROP_OFF
     * -> IN_DELIVERY -> COMPLETED, plus CANCELED/CANCELLED, FAILED,
     * RETURNED/IN_RETURN.
     */
    private function mapGrabStatus(string $grabStatus): ?TransactionShipmentStatus
    {
        return match (strtoupper($grabStatus)) {
            'ALLOCATING' => TransactionShipmentStatus::PENDING,
            'PENDING_PICKUP' => TransactionShipmentStatus::DRIVER_ASSIGNED,
            'PICKING_UP' => TransactionShipmentStatus::DRIVER_ASSIGNED, // driver menuju toko
            'PENDING_DROP_OFF' => TransactionShipmentStatus::PICKED_UP,       // parcel sudah diambil
            'IN_DELIVERY' => TransactionShipmentStatus::ON_THE_WAY,      // menuju/di tempat penerima
            'COMPLETED' => TransactionShipmentStatus::DELIVERED,
            'CANCELED', 'CANCELLED' => TransactionShipmentStatus::CANCELED,
            'RETURNED', 'IN_RETURN' => TransactionShipmentStatus::CANCELED,        // dikembalikan
            'FAILED' => TransactionShipmentStatus::FAILED,
            default => null,
        };
    }
}
