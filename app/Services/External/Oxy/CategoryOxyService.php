<?php

namespace App\Services\External\Oxy;

use App\Helpers\ErrorHandler;
use Exception;
use Illuminate\Support\Facades\Http;

class CategoryOxyService
{
    public static function getCategories(
        string $token,
        ?string $name = null,
        ?string $code = null,
        int $page = 0,
        int $size = 20
    ) {
        $baseUrl = env('OXY_BASE_URL_API');

        $url = $baseUrl . '/membership/api/category';

        $queryParams = [
            'name' => $name,
            'code' => $code,
            'page' => $page,
            'size' => $size,
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

    public static function getSubCategories(
        string $token,
        ?string $name = null,
        ?string $code = null,
        int $page = 0,
        int $size = 20
    ) {
        $baseUrl = env('OXY_BASE_URL_API');

        $url = $baseUrl . '/membership/api/subcategory';

        $queryParams = [
            'name' => $name,
            'code' => $code,
            'page' => $page - 1, // why 1 (karena di api page dimulai dari 0 = 1st page)
            'size' => $size,
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
