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
    public function images(string $oxyCategoryId)
    {
        try {
            $images = CategoryImage::where('oxy_category_id', $oxyCategoryId)->get();

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

    public function getCategoriesWithImages(Request $request)
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

            if (($response['success'] ?? false) !== true) {
                return response()->json(
                    $response['error'],
                    $response['code'] ?? 500
                );
            }

            $categories = collect($response['data']['data']);

            // Ambil semua oxy_category_id
            $categoryIds = $categories
                ->pluck('categoryId')
                ->filter()
                ->values();

            if ($categoryIds->isEmpty()) {
                return response()->json($response['data'], 200);
            }

            // Ambil semua images (1 query)
            $images = CategoryImage::whereIn(
                'oxy_category_id',
                $categoryIds
            )
                ->get()
                ->groupBy('oxy_category_id');

            // Inject images sebagai array string
            $categories = $categories->map(function ($category) use ($images) {
                $categoryId = $category['categoryId'];

                $category['images'] = CategoryImageResource::collection(
                    $images->get($categoryId, collect())
                );

                return $category;
            });

            $response['data']['data'] = $categories->values();

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

            $response = CategoryOxyService::getSubCategories(
                token: $oxyAccessToken,
                name: $request->name ?? null,
                code: $request->code ?? null,
                page: $request->page ?? 0,
                size: $request->size ?? 20,
                categoryId: $request->categoryId ?? null
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
