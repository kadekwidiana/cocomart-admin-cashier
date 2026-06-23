<?php

namespace App\Services\External\Grab;

use App\Helpers\ErrorHandler;
use Exception;
use Illuminate\Support\Facades\Http;

class GrabDeliveryService
{

    protected static function deliveryBaseUrl(): string
    {
        $endpoint = rtrim((string) config('services.grab.endpoint'), '/');
        $path = trim((string) config('services.grab.delivery_path'), '/');

        return $endpoint . '/' . $path . '/v1';
    }

    public static function quote(string $token, array $payload)
    {
        $url = self::deliveryBaseUrl() . '/deliveries/quotes';

        try {
            $response = Http::withToken($token)->post($url, $payload);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'code'    => $response->status(),
                    'message' => $response->json('message') ?? 'Failed to get delivery quote.',
                    'error'   => $response->json(),
                ];
            }

            return [
                'success' => true,
                'code'    => $response->status(),
                'data'    => $response->json(),
            ];
        } catch (Exception $e) {
            return ErrorHandler::apiErrorResponse($e->getMessage());
        }
    }

    public static function createDelivery(string $token, array $payload)
    {
        $url = self::deliveryBaseUrl() . '/deliveries';

        try {
            $response = Http::withToken($token)->post($url, $payload);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'code'    => $response->status(),
                    'message' => $response->json('message') ?? 'Failed to create delivery.',
                    'error'   => $response->json(),
                ];
            }

            return [
                'success' => true,
                'code'    => $response->status(),
                'data'    => $response->json(),
            ];
        } catch (Exception $e) {
            return ErrorHandler::apiErrorResponse($e->getMessage());
        }
    }

    public static function getDelivery(string $token, string $deliveryId)
    {
        $url = self::deliveryBaseUrl() . '/deliveries/' . $deliveryId;

        try {
            $response = Http::withToken($token)->get($url);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'code'    => $response->status(),
                    'message' => $response->json('message') ?? 'Failed to get delivery.',
                    'error'   => $response->json(),
                ];
            }

            return [
                'success' => true,
                'code'    => $response->status(),
                'data'    => $response->json(),
            ];
        } catch (Exception $e) {
            return ErrorHandler::apiErrorResponse($e->getMessage());
        }
    }

    public static function cancelDelivery(string $token, string $deliveryId)
    {
        $url = self::deliveryBaseUrl() . '/deliveries/' . $deliveryId;

        try {
            $response = Http::withToken($token)->delete($url);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'code'    => $response->status(),
                    'message' => $response->json('message') ?? 'Failed to cancel delivery.',
                    'error'   => $response->json(),
                ];
            }

            return [
                'success' => true,
                'code'    => $response->status(),
                'data'    => $response->json(),
            ];
        } catch (Exception $e) {
            return ErrorHandler::apiErrorResponse($e->getMessage());
        }
    }
}
