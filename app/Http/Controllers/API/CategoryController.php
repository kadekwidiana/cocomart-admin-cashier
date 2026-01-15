<?php

namespace App\Http\Controllers\API;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryImageResource;
use App\Models\CategoryImage;
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
}
