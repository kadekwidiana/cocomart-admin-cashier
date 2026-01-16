<?php

namespace App\Http\Controllers\API;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\StoreImageResource;
use App\Models\OxyApiToken;
use App\Models\StoreImage;
use Illuminate\Http\Request;
use App\Services\External\Oxy\StoreOxyService;

class StoreController extends Controller
{
    public function images(string $oxy_store_id)
    {
        try {
            $images = StoreImage::where('oxy_store_id', $oxy_store_id)->get();

            return ApiResponse::success(StoreImageResource::collection($images), 'Images retrieved successfully');
        } catch (\Exception $e) {
            return ApiResponse::error(
                [
                    'detail' => $e->getMessage(),
                ]
            );
        }
    }

    public function getLocations(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'nullable|string',
                'code' => 'nullable|string',
            ]);

            $oxyAccessToken = OxyApiToken::getAccessToken();

            $response = StoreOxyService::getLocations(
                token: $oxyAccessToken,
                name: $validated['name'] ?? null,
                code: $validated['code'] ?? null
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
