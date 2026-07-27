<?php

namespace App\Services;

use App\Enums\TransactionFulfillmentType;
use App\Enums\TransactionStatus;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

class TransactionPaymentService
{
    public static function markPaidAndDispatch(Transaction $transaction): void
    {
        if ($transaction->status === TransactionStatus::PENDING) {
            $transaction->update(['status' => TransactionStatus::PAID]);
        }

        if ($transaction->fulfillment_type !== TransactionFulfillmentType::SHIPMENT) {
            return;
        }

        $transaction->loadMissing('shipment');
        $shipment = $transaction->shipment;

        if (! $shipment || ! $shipment->grab_vehicle_type || $shipment->grab_delivery_id) {
            return;
        }

        $result = GrabDispatchService::dispatch($transaction);

        if (! ($result['success'] ?? false)) {
            Log::error('[Paylabs] Auto-dispatch Grab gagal setelah pembayaran sukses', [
                'transaction_id' => $transaction->id,
                'error' => $result['message'] ?? null,
            ]);
        }
    }
}
