<?php

namespace App\Http\Controllers\API;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\ItemMasterImageResource;
use App\Models\ItemMasterImage;
use App\Models\OxyApiToken;
use App\Services\External\Oxy\ItemMasterOxyService;
use Illuminate\Http\Request;

class ItemMasterController extends Controller
{
    public function images(string $oxyItemMasterId)
    {
        try {
            $images = ItemMasterImage::where('oxy_item_master_id', $oxyItemMasterId)->get();

            return ApiResponse::success(ItemMasterImageResource::collection($images), 'Images retrieved successfully');
        } catch (\Exception $e) {
            return ApiResponse::error(
                [
                    'detail' => $e->getMessage(),
                ]
            );
        }
    }

    public function getItemMasters(Request $request)
    {
        try {
            $oxyAccessToken = OxyApiToken::getAccessToken();

            $response = ItemMasterOxyService::getItemMasters(
                token: $oxyAccessToken,
                name: $request->name ?? null,
                code: $request->code ?? null,
                page: $request->page ?? 0,
                size: $request->size ?? 20,
            );

            if (!$response['success']) {
                return response()->json($response['error'], $response['code'] ?? 500);
            }

            return response()->json($response['data'], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Internal server error',
            ], 500);
        }
    }

    public function getItemMasterDetail(Request $request)
    {
        try {
            $oxyAccessToken = OxyApiToken::getAccessToken();

            $response = ItemMasterOxyService::getItemMasterDetail(
                token: $oxyAccessToken,
                itemMasterId: $request->itemMasterId ?? null,
                locationId: $request->locationId ?? null,
                page: $request->page ?? 0,
                size: $request->size ?? 20,
            );

            if (!$response['success']) {
                return response()->json($response['error'], $response['code'] ?? 500);
            }

            return response()->json($response['data'], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Internal server error',
            ], 500);
        }
    }

    public function getItemMasterPrice(string $oxyItemMasterId)
    {
        try {
            $oxyAccessToken = OxyApiToken::getAccessToken();

            $response = ItemMasterOxyService::getItemMasterPrice(
                token: $oxyAccessToken,
                id: $oxyItemMasterId ?? null,
            );

            if (!$response['success']) {
                return response()->json($response['error'], $response['code'] ?? 500);
            }

            return response()->json($response['data'], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Internal server error',
            ], 500);
        }
    }

    public function getItemMasterStock(Request $request)
    {
        try {
            $oxyAccessToken = OxyApiToken::getAccessToken();

            $response = ItemMasterOxyService::getItemMasterStock(
                token: $oxyAccessToken,
                name: $request->name ?? null,
                code: $request->code ?? null,
                barcode: $request->barcode ?? null,
                page: $request->page ?? 0,
                size: $request->size ?? 20,
            );

            if (!$response['success']) {
                return response()->json($response['error'], $response['code'] ?? 500);
            }

            return response()->json($response['data'], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Internal server error',
            ], 500);
        }
    }

    public function getItemMasterStockLocation(string $oxyItemMasterId)
    {
        try {
            $oxyAccessToken = OxyApiToken::getAccessToken();

            $response = ItemMasterOxyService::getItemMasterStockLocation(
                token: $oxyAccessToken,
                id: $oxyItemMasterId ?? null,
            );

            if (!$response['success']) {
                return response()->json($response['error'], $response['code'] ?? 500);
            }

            return response()->json($response['data'], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Internal server error',
            ], 500);
        }
    }
}
