<?php

namespace App\Services\External\Oxy;

use App\Helpers\ErrorHandler;
use Exception;
use Illuminate\Support\Facades\Http;

class StoreOxyService
{
    public static function getLocations(
        string $token,
        ?string $name = null,
        ?string $code = null
    ) {
        $baseUrl = env('OXY_BASE_URL_API');

        $url = $baseUrl . '/membership/api/location';

        $queryParams = [
            'name' => $name,
            'code' => $code
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer " . $token
            ])->get($url, $queryParams);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'code'    => $response->status(),
                    'message' => $response->json('message') ?? 'Something went wrong.',
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
