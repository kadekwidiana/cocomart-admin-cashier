<?php

namespace App\Services\External\Paylabs;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaylabsClient
{
    public static function createQris(
        string $merchantTradeNo,
        float $amount,
        string $productName,
        ?string $notifyUrl = null,
        int $expire = 3600
    ): array {
        $endpoint = '/payment/v2.3/qris/create';

        $body = [
            'requestId'       => self::generateRequestId(),
            'merchantId'      => (string) config('paylabs.merchant_id'),
            'paymentType'     => 'QRIS',
            'amount'          => number_format($amount, 2, '.', ''),
            'merchantTradeNo' => $merchantTradeNo,
            'notifyUrl'       => $notifyUrl,
            'expire'          => $expire,
            'feeType'         => 'BEN',
            'productName'     => $productName,
        ];

        return self::post($endpoint, $body);
    }

    public static function queryQris(string $merchantTradeNo): array
    {
        $endpoint = '/payment/v2.3/qris/query';

        $body = [
            'requestId'       => self::generateRequestId(),
            'merchantId'      => (string) config('paylabs.merchant_id'),
            'merchantTradeNo' => $merchantTradeNo,
            'paymentType'     => 'QRIS',
        ];

        return self::post($endpoint, $body);
    }

    public static function cancelQris(string $merchantTradeNo): array
    {
        $endpoint = '/payment/v2.3/qris/cancel';

        $body = [
            'requestId'       => self::generateRequestId(),
            'merchantId'      => (string) config('paylabs.merchant_id'),
            'merchantTradeNo' => $merchantTradeNo,
            'paymentType'     => 'QRIS',
        ];

        return self::post($endpoint, $body);
    }

    public static function createVa(
        string $paymentType,
        string $merchantTradeNo,
        float $amount,
        string $productName,
        string $payer,
        ?string $notifyUrl = null,
        int $expire = 3600
    ): array {
        $endpoint = '/payment/v2.3/va/create';

        $body = [
            'requestId'       => self::generateRequestId(),
            'merchantId'      => (string) config('paylabs.merchant_id'),
            'paymentType'     => $paymentType,
            'amount'          => number_format($amount, 2, '.', ''),
            'merchantTradeNo' => $merchantTradeNo,
            'notifyUrl'       => $notifyUrl,
            'expire'          => $expire,
            'feeType'         => 'BEN',
            'productName'     => $productName,
            'payer'           => $payer,
        ];

        return self::post($endpoint, $body);
    }

    public static function queryVa(string $merchantTradeNo, string $paymentType): array
    {
        $endpoint = '/payment/v2.3/va/query';

        $body = [
            'requestId'       => self::generateRequestId(),
            'merchantId'      => (string) config('paylabs.merchant_id'),
            'merchantTradeNo' => $merchantTradeNo,
            'paymentType'     => $paymentType,
        ];

        return self::post($endpoint, $body);
    }

    protected static function post(string $endpoint, array $body): array
    {
        try {
            $body = array_filter($body, fn($v) => $v !== null);

            $timestamp = self::timestamp();
            $signature = (new PaylabsSigner())->sign('POST', $endpoint, $body, $timestamp);

            $baseUrl = rtrim(config('paylabs.base_url'), '/');
            $minifiedBody = json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json;charset=utf-8',
                'X-TIMESTAMP'  => $timestamp,
                'X-SIGNATURE'  => $signature,
                'X-PARTNER-ID' => (string) config('paylabs.merchant_id'),
                'X-REQUEST-ID' => $body['requestId'],
            ])
                ->withBody($minifiedBody, 'application/json;charset=utf-8')
                ->post($baseUrl . $endpoint);

            $data = $response->json();

            if ($data === null) {
                return [
                    'success' => false,
                    'message' => 'Invalid response from Paylabs',
                    'code'    => $response->status(),
                    'error'   => $response->body(),
                ];
            }

            if (($data['errCode'] ?? null) !== '0') {
                return [
                    'success' => false,
                    'message' => $data['errCodeDes'] ?? 'Paylabs returned an error',
                    'code'    => $response->status(),
                    'error'   => $data,
                ];
            }

            return [
                'success' => true,
                'data'    => $data,
            ];
        } catch (\Throwable $e) {
            Log::error('Paylabs API call failed', [
                'endpoint' => $endpoint,
                'error'    => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'code'    => 500,
            ];
        }
    }

    protected static function generateRequestId(): string
    {
        return now()->format('YmdHis') . bin2hex(random_bytes(8));
    }

    protected static function timestamp(): string
    {
        return now()->format('Y-m-d\TH:i:s.v') . now()->format('P');
    }
}
