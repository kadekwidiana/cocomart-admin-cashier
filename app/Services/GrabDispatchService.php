<?php

namespace App\Services;

use App\Enums\TransactionShipmentStatus;
use App\Models\GrabApiToken;
use App\Models\OxyApiToken;
use App\Models\Transaction;
use App\Services\External\Grab\GrabDeliveryPayloadBuilder;
use App\Services\External\Grab\GrabDeliveryPayloadValidator;
use App\Services\External\Grab\GrabDeliveryService;
use Illuminate\Support\Facades\DB;

class GrabDispatchService
{
    /**
     * Buat Grab delivery untuk transaksi SHIPMENT. Idempotent — no-op kalau
     * `grab_delivery_id` sudah terisi. `vehicleType` dibaca dari
     * `shipment->grab_vehicle_type` (tersimpan sejak langkah quote, sebelum
     * pembayaran selesai), bukan dari parameter caller.
     */
    public static function dispatch(Transaction $transaction): array
    {
        $shipment = $transaction->shipment;

        if (! $shipment) {
            return ['success' => false, 'message' => 'Shipment data not found for this transaction'];
        }

        if ($shipment->grab_delivery_id) {
            return ['success' => true, 'data' => null, 'message' => 'Already dispatched'];
        }

        $vehicleType = $shipment->grab_vehicle_type;

        if (! $vehicleType) {
            return ['success' => false, 'message' => 'vehicleType belum diketahui untuk transaksi ini'];
        }

        $grabToken = GrabApiToken::getValidAccessToken();

        if (! $grabToken) {
            return ['success' => false, 'message' => 'Grab access token not available'];
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

        if (! empty($payloadErrors)) {
            return [
                'success' => false,
                'message' => $payloadErrors[0] ?? 'Invalid Grab delivery payload',
                'errors' => $payloadErrors,
            ];
        }

        $createRes = GrabDeliveryService::createDelivery($grabToken, $deliveryPayload);

        if (! ($createRes['success'] ?? false)) {
            return [
                'success' => false,
                'message' => $createRes['message'] ?? 'Grab create delivery failed',
                'error' => $createRes['error'] ?? null,
            ];
        }

        $shippingCost = $createRes['data']['quote']['amount'] ?? $shipment->grab_shipping_cost ?? 0;

        DB::transaction(function () use ($shipment, $transaction, $createRes, $shippingCost) {
            $shipment->grab_delivery_id = $createRes['data']['deliveryID'] ?? null;
            $shipment->grab_shipping_cost = $shippingCost;
            $shipment->status = TransactionShipmentStatus::PENDING;
            $shipment->grab_json_response = json_encode($createRes['data']);
            $shipment->save();

            $transaction->update([
                'shipping_cost' => $shippingCost,
                'total' => $transaction->subtotal + $shippingCost,
            ]);
        });

        return ['success' => true, 'data' => $createRes['data']];
    }
}
