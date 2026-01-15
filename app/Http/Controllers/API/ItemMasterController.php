<?php

namespace App\Http\Controllers\API;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\ItemMasterImageResource;
use App\Models\ItemMasterImage;
use Illuminate\Http\Request;

class ItemMasterController extends Controller
{
    public function images(string $oxy_item_master_id)
    {
        try {
            $images = ItemMasterImage::where('oxy_item_master_id', $oxy_item_master_id)->get();

            return ApiResponse::success(ItemMasterImageResource::collection($images), 'Images retrieved successfully');
        } catch (\Exception $e) {
            return ApiResponse::error(
                [
                    'detail' => $e->getMessage(),
                ]
            );
        }
    }
}
