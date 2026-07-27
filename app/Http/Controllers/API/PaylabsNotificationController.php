<?php

namespace App\Http\Controllers\API;

use App\Enums\TransactionStatus;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Services\External\Paylabs\PaylabsVerifier;
use App\Services\TransactionPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaylabsNotificationController extends Controller
{
    public function notify(Request $request)
    {
        $timestamp = $request->header('X-TIMESTAMP');
        $signature = $request->header('X-SIGNATURE');
        $requestId = $request->header('X-REQUEST-ID');
        $body = $request->json()->all();

        if (! $timestamp || ! $signature) {
            Log::warning('Paylabs notify: missing signature headers');

            return response()->json(['errCode' => '1', 'errCodeDes' => 'Missing signature headers'], 400);
        }

        try {
            $isValid = (new PaylabsVerifier)->verify(
                method: 'POST',
                endpoint: '/'.ltrim($request->path(), '/'),
                body: $body,
                timestamp: $timestamp,
                signature: $signature,
            );
        } catch (\Throwable $e) {
            Log::error('Paylabs notify: signature verification failed', ['error' => $e->getMessage()]);

            return response()->json(['errCode' => '1', 'errCodeDes' => 'Signature verification error'], 500);
        }

        if (! $isValid) {
            Log::warning('Paylabs notify: invalid signature', ['body' => $body]);

            return response()->json(['errCode' => '1', 'errCodeDes' => 'Invalid signature'], 401);
        }

        $merchantTradeNo = $body['merchantTradeNo'] ?? null;
        $status = $body['status'] ?? null; // 01 Pending, 02 Success, 09 Failed

        $transaction = Transaction::query()
            ->where('payment_token', $merchantTradeNo)
            ->first();

        if (! $transaction) {
            Log::warning('Paylabs notify: transaction not found', ['merchantTradeNo' => $merchantTradeNo]);

            return response()->json([
                'merchantId' => $body['merchantId'] ?? config('paylabs.merchant_id'),
                'requestId' => $requestId ?? $body['requestId'] ?? null,
                'errCode' => '0',
            ]);
        }

        if ($status === '02') {
            TransactionPaymentService::markPaidAndDispatch($transaction);
            Log::info('Paylabs payment success', ['transaction_id' => $transaction->id]);
        } elseif ($status === '09') {
            $transaction->update(['status' => TransactionStatus::CANCELED]);
            Log::info('Paylabs payment failed', ['transaction_id' => $transaction->id]);
        }

        return response()->json([
            'merchantId' => $body['merchantId'] ?? config('paylabs.merchant_id'),
            'requestId' => $requestId ?? $body['requestId'] ?? null,
            'errCode' => '0',
        ]);
    }
}
