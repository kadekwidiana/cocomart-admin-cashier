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
            $validated = $request->validate([
                'name' => 'nullable|string',
                'code' => 'nullable|string',
                'page' => 'nullable|integer|min:1',
                'size' => 'nullable|integer|min:1|max:100',
            ]);

            $oxyAccessToken = OxyApiToken::getAccessToken();

            $response = CategoryOxyService::getCategories(
                token: $oxyAccessToken,
                name: $validated['name'] ?? null,
                code: $validated['code'] ?? null,
                page: $validated['page'] ?? 1,
                size: $validated['size'] ?? 10,
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
