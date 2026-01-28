<?php

namespace App\Http\Controllers\API;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\LocationImageResource;
use App\Models\LocationImage;
use App\Models\OxyApiToken;
use App\Models\StoreImage;
use Illuminate\Http\Request;
use App\Services\External\Oxy\LocationOxyService;

class LocationController extends Controller
{
    public function images(string $oxyLocationId)
    {
        try {
            $images = LocationImage::where('oxy_location_id', $oxyLocationId)->get();

            return ApiResponse::success(LocationImageResource::collection($images), 'Images retrieved successfully');
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
            $oxyAccessToken = OxyApiToken::getAccessToken();

            $response = LocationOxyService::getLocations(
                token: $oxyAccessToken,
                name: $request->name ?? null,
                code: $request->code ?? null,
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
