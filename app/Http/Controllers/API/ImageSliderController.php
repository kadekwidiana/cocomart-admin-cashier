<?php

namespace App\Http\Controllers\API;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\ImageSliderResource;
use App\Http\Resources\PaginationResource;
use App\Models\ImageSlider;
use Illuminate\Http\Request;

class ImageSliderController extends Controller
{
    public function index()
    {
        try {
            $page = $request->page ?? 1;
            $size = $request->size ?? 10;

            $imageSliders = ImageSlider::paginate($size, ['*'], 'page', $page);

            $imageSliders->appends([
                'page' => $page,
                'size' => $size,
            ]);

            return ApiResponse::success([
                'data' => ImageSliderResource::collection($imageSliders),
                'pagination' => new PaginationResource($imageSliders),
            ], 'Image Sliders retrieved successfully');
        } catch (\Exception $e) {
            return ApiResponse::error(
                [
                    'detail' => $e->getMessage(),
                ]
            );
        }
    }
}
