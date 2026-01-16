<?php

namespace App\Http\Controllers\API;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\PaginationResource;
use App\Http\Resources\PromoResource;
use App\Models\Promo;
use Illuminate\Http\Request;

class PromoController extends Controller
{
    public function index(Request $request)
    {
        try {
            $page = $request->page ?? 1;
            $size = $request->size ?? 10;
            $title = $request->title ?? null;

            $promos = Promo::query();

            if ($title) {
                $promos->where('title', 'like', '%' . $title . '%');
            }

            $promos = $promos->paginate($size, ['*'], 'page', $page);

            $promos->appends([
                'page' => $page,
                'size' => $size,
                'title' => $title,
            ]);

            return ApiResponse::success([
                'data' => PromoResource::collection($promos),
                'pagination' => new PaginationResource($promos),
            ], 'Promos retrieved successfully');
        } catch (\Exception $e) {
            return ApiResponse::error(
                [
                    'detail' => $e->getMessage(),
                ]
            );
        }
    }

    public function count()
    {
        try {
            $count = Promo::count();
            return ApiResponse::success($count, 'Promos count retrieved successfully');
        } catch (\Exception $e) {
            return ApiResponse::error(
                [
                    'detail' => $e->getMessage(),
                ]
            );
        }
    }
}
