<?php

namespace App\Http\Controllers\API;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryImageResource;
use App\Models\CategoryImage;
use App\Models\OxyApiToken;
use App\Services\External\Oxy\CategoryOxyService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function images(string $oxy_category_id)
    {
        try {
            $images = CategoryImage::where('oxy_category_id', $oxy_category_id)->get();

            return ApiResponse::success(CategoryImageResource::collection($images), 'Images retrieved successfully');
        } catch (\Exception $e) {
            return ApiResponse::error(
                [
                    'detail' => $e->getMessage(),
                ]
            );
        }
    }

    public function getCategories(Request $request)
    {
        try {
            $oxyAccessToken = OxyApiToken::getAccessToken();

            $response = CategoryOxyService::getCategories(
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

    public function getSubCategories(Request $request)
    {
        try {
            $oxyAccessToken = OxyApiToken::getAccessToken();

            $response = CategoryOxyService::getCategories(
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
}
