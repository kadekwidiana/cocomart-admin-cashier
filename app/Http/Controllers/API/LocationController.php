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

    public function getLocationsWithImages(Request $request)
    {
        try {
            $oxyAccessToken = OxyApiToken::getAccessToken();

            $response = LocationOxyService::getLocations(
                token: $oxyAccessToken,
                name: $request->name ?? null,
                code: $request->code ?? null,
            );

            if (($response['success'] ?? false) !== true) {
                return response()->json(
                    $response['error'],
                    $response['code'] ?? 500
                );
            }

            $locations = collect($response['data']['data']);

            // 1. Ambil semua OXY location ID (pakai `id`)
            $locationIds = $locations
                ->pluck('id')
                ->filter()
                ->values();

            if ($locationIds->isEmpty()) {
                return response()->json($response['data'], 200);
            }

            // 2. Ambil semua images (1 query)
            $images = LocationImage::whereIn(
                'oxy_location_id',
                $locationIds
            )
                ->get()
                ->groupBy('oxy_location_id');

            // 3. Inject images (array string)
            $locations = $locations->map(function ($location) use ($images) {
                $locationId = $location['id'];

                $location['images'] = LocationImageResource::collection(
                    $images->get($locationId, collect())
                );

                return $location;
            });

            $response['data']['data'] = $locations->values();

            return response()->json($response['data'], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Internal server error',
            ], 500);
        }
    }
}
