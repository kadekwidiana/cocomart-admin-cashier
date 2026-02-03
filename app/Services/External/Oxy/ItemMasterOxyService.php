<?php

namespace App\Services\External\Oxy;

use App\Helpers\ErrorHandler;
use Exception;
use Illuminate\Support\Facades\Http;

class ItemMasterOxyService
{
    public static function getItemMasters(
        string $token,
        ?string $name = null,
        ?string $code = null,
        ?string $barcode = null,
        int $page = 0,
        int $size = 20
    ) {
        $baseUrl = env('OXY_BASE_URL_API');

        $url = $baseUrl . '/membership/api/itemmaster';

        $queryParams = [
            'name' => $name,
            'code' => $code,
            'barcode' => $barcode,
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

    public static function getItemMasterDetail(
        string $token,
        ?string $itemMasterId,
        ?string $locationId,
        int $page = 0,
        int $size = 20,
        ?string $categoryId = null,
        ?string $subcategoryId = null
    ) {
        $baseUrl = env('OXY_BASE_URL_API');

        $url = $baseUrl . '/membership/api/itemmaster/detail';

        $queryParams = [
            'itemMasterId' => $itemMasterId,
            'locationId' => $locationId,
            'categoryId' => $categoryId,
            'subcategoryId' => $subcategoryId,
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

    public static function getItemMasterPrice(
        string $token,
        string $id
    ) {
        $baseUrl = env('OXY_BASE_URL_API');

        $url = $baseUrl . '/membership/api/itemmaster/prices/' . $id;

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer " . $token
            ])->get($url);

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

    public static function getItemMasterStock(
        string $token,
        ?string $name = null,
        ?string $code = null,
        ?string $barcode = null,
        int $page = 0,
        int $size = 20
    ) {
        $baseUrl = env('OXY_BASE_URL_API');

        $url = $baseUrl . '/membership/api/itemmaster/stock';

        $queryParams = [
            'name' => $name,
            'code' => $code,
            'barcode' => $barcode,
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

    public static function getItemMasterStockLocation(
        string $token,
        string $id
    ) {
        $baseUrl = env('OXY_BASE_URL_API');

        $url = $baseUrl . '/membership/api/itemmaster/stocklocation/' . $id;

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer " . $token
            ])->get($url);

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
