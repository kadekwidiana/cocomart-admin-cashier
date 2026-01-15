<?php

namespace App\Http\Controllers\API;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\StoreImageResource;
use App\Models\StoreImage;
use Illuminate\Http\Request;

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
}
