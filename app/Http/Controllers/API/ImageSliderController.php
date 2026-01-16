<?php

namespace App\Http\Controllers\API;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\ImageSliderResource;
use App\Models\ImageSlider;
use Illuminate\Http\Request;

class ImageSliderController extends Controller
{
    public function index()
    {
        try {
            $imageSliders = ImageSlider::latest()->get();

            return ApiResponse::success(ImageSliderResource::collection($imageSliders), 'Image Sliders retrieved successfully');
        } catch (\Exception $e) {
            return ApiResponse::error(
                [
                    'detail' => $e->getMessage(),
                ]
            );
        }
    }
}
