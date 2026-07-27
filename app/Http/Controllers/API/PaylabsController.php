<?php

namespace App\Http\Controllers\API;

use App\Enums\TransactionStatus;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\Transaction\Paylabs\CancelPaymentRequest;
use App\Http\Requests\API\Transaction\Paylabs\StorePaymentRequest;
use App\Models\Transaction;
use App\Services\External\Paylabs\PaylabsClient;
use App\Services\TransactionPaymentService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class PaylabsController extends Controller
{
    public function store(StorePaymentRequest $request)
    {
        try {
            $validated = $request->validated();

            $transaction = Transaction::query()->findOrFail($validated['transactionId']);

            if ($transaction->status !== TransactionStatus::PENDING) {
                return ApiResponse::error(
                    data: null,
                    message: 'Transaction is not payable (status must be PENDING)',
                    statusCode: Response::HTTP_CONFLICT
                );
            }

            $paymentType = $validated['paymentType'];
            $merchantTradeNo = $transaction->id.'-'.Str::upper(Str::random(5));
            $expireSeconds = 3600;

            $result = $paymentType === 'QRIS'
                ? PaylabsClient::createQris(
                    merchantTradeNo: $merchantTradeNo,
                    amount: (float) $transaction->total,
                    productName: 'Order '.$transaction->id,
                    notifyUrl: config('paylabs.notify_url'),
                    expire: $expireSeconds,
                )
                : PaylabsClient::createVa(
                    paymentType: $paymentType,
                    merchantTradeNo: $merchantTradeNo,
                    amount: (float) $transaction->total,
                    productName: 'Order '.$transaction->id,
                    payer: 'Customer',
                    notifyUrl: config('paylabs.notify_url'),
                    expire: $expireSeconds,
                );

            if (! ($result['success'] ?? false)) {
                return ApiResponse::error(
                    data: $result['error'] ?? null,
                    message: $result['message'] ?? 'Failed to create payment',
                    statusCode: $result['code'] ?? Response::HTTP_BAD_GATEWAY
                );
            }

            $data = $result['data'];

            $expiredAt = isset($data['expiredTime'])
                ? \DateTime::createFromFormat('YmdHis', $data['expiredTime'])
                : now()->addSeconds($expireSeconds);

            $transaction->update([
                'payment_token' => $merchantTradeNo,
                'payment_type' => $paymentType,
            ]);

            return ApiResponse::success(
                data: [
                    'transaction_id' => $transaction->id,
                    'payment_type' => $paymentType,
                    'amount' => $transaction->total,
                    'qr_code' => $data['qrCode'] ?? null,
                    'qris_url' => $data['qrisUrl'] ?? null,
                    'va_code' => $data['vaCode'] ?? null,
                    'expired_at' => $expiredAt,
                ],
                message: 'Payment created successfully'
            );
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(['detail' => 'Transaction not found'], 404);
        } catch (\Throwable $e) {
            return ApiResponse::error(['detail' => $e->getMessage()], 500);
        }
    }

    public function inquiry(string $transactionId)
    {
        try {
            $transaction = Transaction::query()->findOrFail($transactionId);

            if (! $transaction->payment_token || ! $transaction->payment_type) {
                return ApiResponse::error(
                    data: null,
                    message: 'No payment found for this transaction',
                    statusCode: Response::HTTP_NOT_FOUND
                );
            }

            $result = $transaction->payment_type === 'QRIS'
                ? PaylabsClient::queryQris(merchantTradeNo: $transaction->payment_token)
                : PaylabsClient::queryVa(
                    merchantTradeNo: $transaction->payment_token,
                    paymentType: $transaction->payment_type,
                );

            if (! ($result['success'] ?? false)) {
                return ApiResponse::error(
                    data: $result['error'] ?? null,
                    message: $result['message'] ?? 'Failed to fetch payment status',
                    statusCode: $result['code'] ?? Response::HTTP_BAD_GATEWAY
                );
            }

            $status = $result['data']['status'] ?? '01';

            if ($status === '02') {
                TransactionPaymentService::markPaidAndDispatch($transaction);
            }

            return ApiResponse::success(
                data: [
                    'transaction_id' => $transaction->id,
                    'status' => $status,
                    'raw' => $result['data'],
                ],
                message: 'Payment status retrieved successfully'
            );
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(['detail' => 'Transaction not found'], 404);
        } catch (\Throwable $e) {
            return ApiResponse::error(['detail' => $e->getMessage()], 500);
        }
    }

    public function cancel(CancelPaymentRequest $request)
    {
        try {
            $validated = $request->validated();

            $transaction = Transaction::query()->findOrFail($validated['transactionId']);

            if (! $transaction->payment_token || ! $transaction->payment_type) {
                return ApiResponse::error(
                    data: null,
                    message: 'No payment found for this transaction',
                    statusCode: Response::HTTP_NOT_FOUND
                );
            }

            if ($transaction->payment_type !== 'QRIS') {
                return ApiResponse::error(
                    data: null,
                    message: 'Cancel is only supported for QRIS payments',
                    statusCode: Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $result = PaylabsClient::cancelQris(merchantTradeNo: $transaction->payment_token);

            if (! ($result['success'] ?? false)) {
                return ApiResponse::error(
                    data: $result['error'] ?? null,
                    message: $result['message'] ?? 'Failed to cancel payment',
                    statusCode: $result['code'] ?? Response::HTTP_BAD_GATEWAY
                );
            }

            $transaction->update(['status' => TransactionStatus::CANCELED]);

            return ApiResponse::success(
                data: ['transaction_id' => $transaction->id],
                message: 'Payment cancelled successfully'
            );
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(['detail' => 'Transaction not found'], 404);
        } catch (\Throwable $e) {
            return ApiResponse::error(['detail' => $e->getMessage()], 500);
        }
    }
}
