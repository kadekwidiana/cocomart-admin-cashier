<?php

namespace App\Http\Controllers\API;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\ItemMaster\AddToWishListRequest;
use App\Http\Requests\API\ItemMaster\GetItemMasterByIdsRequest;
use App\Http\Resources\AddToWishListResponseResource;
use App\Http\Resources\ItemMasterImageResource;
use App\Models\ItemMasterImage;
use App\Models\OxyApiToken;
use App\Models\WishList;
use App\Services\External\Oxy\ItemMasterOxyService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class ItemMasterController extends Controller
{
    public function images(string $oxyItemMasterId)
    {
        try {
            $images = ItemMasterImage::where('oxy_item_master_id', $oxyItemMasterId)->get();

            return ApiResponse::success(ItemMasterImageResource::collection($images), 'Images retrieved successfully');
        } catch (\Exception $e) {
            return ApiResponse::error(
                [
                    'detail' => $e->getMessage(),
                ]
            );
        }
    }

    public function getItemMasters(Request $request)
    {
        try {
            $oxyAccessToken = OxyApiToken::getAccessToken();

            $response = ItemMasterOxyService::getItemMasters(
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
        } catch (\Exception $e) {
            return ApiResponse::error([
                'detail' => $e->getMessage(),
            ]);
        }
    }

    public function getItemMasterDetail(Request $request)
    {
        try {
            $oxyAccessToken = OxyApiToken::getAccessToken();

            $response = ItemMasterOxyService::getItemMasterDetail(
                token: $oxyAccessToken,
                itemMasterId: $request->itemMasterId ?? null,
                locationId: $request->locationId ?? null,
                page: $request->page ?? 0,
                size: $request->size ?? 20,
                categoryId: $request->categoryId ?? null,
                subcategoryId: $request->subcategoryId ?? null
            );

            if (!$response['success']) {
                return response()->json($response['error'], $response['code'] ?? 500);
            }

            return response()->json($response['data'], 200);
        } catch (\Exception $e) {
            return ApiResponse::error([
                'detail' => $e->getMessage(),
            ]);
        }
    }

    public function getItemMasterPrice(string $oxyItemMasterId)
    {
        try {
            $oxyAccessToken = OxyApiToken::getAccessToken();

            $response = ItemMasterOxyService::getItemMasterPrice(
                token: $oxyAccessToken,
                id: $oxyItemMasterId ?? null,
            );

            if (!$response['success']) {
                return response()->json($response['error'], $response['code'] ?? 500);
            }

            return response()->json($response['data'], 200);
        } catch (\Exception $e) {
            return ApiResponse::error([
                'detail' => $e->getMessage(),
            ]);
        }
    }

    public function getItemMasterStock(Request $request)
    {
        try {
            $oxyAccessToken = OxyApiToken::getAccessToken();

            $response = ItemMasterOxyService::getItemMasterStock(
                token: $oxyAccessToken,
                name: $request->name ?? null,
                code: $request->code ?? null,
                barcode: $request->barcode ?? null,
                page: $request->page ?? 0,
                size: $request->size ?? 20,
            );

            if (!$response['success']) {
                return response()->json($response['error'], $response['code'] ?? 500);
            }

            return response()->json($response['data'], 200);
        } catch (\Exception $e) {
            return ApiResponse::error([
                'detail' => $e->getMessage(),
            ]);
        }
    }

    public function getItemMasterStockLocation(string $oxyItemMasterId)
    {
        try {
            $oxyAccessToken = OxyApiToken::getAccessToken();

            $response = ItemMasterOxyService::getItemMasterStockLocation(
                token: $oxyAccessToken,
                id: $oxyItemMasterId ?? null,
            );

            if (!$response['success']) {
                return response()->json($response['error'], $response['code'] ?? 500);
            }

            return response()->json($response['data'], 200);
        } catch (\Exception $e) {
            return ApiResponse::error([
                'detail' => $e->getMessage(),
            ]);
        }
    }

    public function checkIsWishlist(string $oxyItemMasterId, string $oxyCustomerId)
    {
        try {
            $exists = WishList::where('oxy_customer_id', $oxyCustomerId)
                ->where('oxy_item_master_id', $oxyItemMasterId)
                ->exists();

            return ApiResponse::success(
                $exists,
                $exists ? 'Wishlist found' : 'Wishlist not found'
            );
        } catch (\Exception $e) {
            return ApiResponse::error([
                'detail' => $e->getMessage(),
            ]);
        }
    }

    public function addToWishlist(AddToWishListRequest $request)
    {
        try {
            $validated = $request->validated();

            $wishList = WishList::firstOrCreate(
                [
                    'oxy_customer_id' => $validated['oxyCustomerId'],
                    'oxy_item_master_id' => $validated['oxyItemMasterId'],
                ]
            );

            return ApiResponse::success(
                new AddToWishListResponseResource($wishList),
                $wishList->wasRecentlyCreated
                    ? 'Wishlist added successfully'
                    : 'Item already in wishlist'
            );
        } catch (\Exception $e) {
            return ApiResponse::error([
                'detail' => $e->getMessage(),
            ]);
        }
    }

    public function removeFromWishlist(string $oxyItemMasterId, string $oxyCustomerId)
    {
        try {
            $wishList = WishList::where('oxy_customer_id', $oxyCustomerId)
                ->where('oxy_item_master_id', $oxyItemMasterId)
                ->first();

            if (!$wishList) {
                return ApiResponse::error(
                    data: [
                        'detail' => 'Wishlist not found',
                    ],
                    message: 'Wishlist not found',
                    statusCode: Response::HTTP_NOT_FOUND
                );
            }

            $wishList->delete();

            return ApiResponse::success(
                null,
                'Wishlist removed successfully'
            );
        } catch (\Exception $e) {
            return ApiResponse::error([
                'detail' => $e->getMessage(),
            ]);
        }
    }

    public function getItemMasterWishlistByCustomer(string $oxyCustomerId)
    {
        try {
            $oxyAccessToken = OxyApiToken::getAccessToken();

            $itemMasters = [];

            $wishLists = WishList::where('oxy_customer_id', $oxyCustomerId)->get();

            foreach ($wishLists as $wishList) {
                $response = ItemMasterOxyService::getItemMasterDetail(
                    token: $oxyAccessToken,
                    itemMasterId: $wishList->oxy_item_master_id,
                    locationId: null,
                    page: 0,
                    size: 1
                );

                //  guard ketat untuk response OXY
                if (
                    ($response['success'] ?? false) !== true ||
                    !isset($response['data']['data']) ||
                    empty($response['data']['data']) ||
                    !isset($response['data']['data'][0])
                ) {
                    // optional: log untuk monitoring
                    Log::warning('Failed get item master from OXY', [
                        'oxy_item_master_id' => $wishList->oxy_item_master_id,
                        'response' => $response,
                    ]);

                    continue; // skip item ini
                }

                $itemMasters[] = $response['data']['data'][0];
            }

            return ApiResponse::success(
                $itemMasters,
                'Wishlist retrieved successfully'
            );
        } catch (\Exception $e) {
            return ApiResponse::error([
                'detail' => $e->getMessage(),
            ]);
        }
    }

    public function getItemMasterByIds(GetItemMasterByIdsRequest $request)
    {
        try {
            $oxyAccessToken = OxyApiToken::getAccessToken();

            $validated = $request->validated();

            $itemMasters = [];

            foreach ($validated['oxyItemMasterIds'] as $id) {
                $response = ItemMasterOxyService::getItemMasterDetail(
                    token: $oxyAccessToken,
                    itemMasterId: $id,
                    locationId: $validated['oxyLocationId'] ?? null,
                    page: 0,
                    size: 1
                );

                //  guard ketat untuk response OXY
                if (
                    ($response['success'] ?? false) !== true ||
                    !isset($response['data']['data']) ||
                    empty($response['data']['data']) ||
                    !isset($response['data']['data'][0])
                ) {
                    // optional: log untuk monitoring
                    Log::warning('Failed get item master from OXY', [
                        'oxy_item_master_id' => $id,
                        'response' => $response,
                    ]);

                    continue; // skip item ini
                }

                $itemMasters[] = $response['data']['data'][0];
            }

            return ApiResponse::success(
                $itemMasters,
                'Item masters retrieved successfully'
            );
        } catch (\Exception $e) {
            return ApiResponse::error([
                'detail' => $e->getMessage(),
            ]);
        }
    }
}
