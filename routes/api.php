<?php

use App\Helpers\ApiResponse;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\ImageSliderController;
use App\Http\Controllers\API\ItemMasterController;
use App\Http\Controllers\API\PromoController;
use App\Http\Controllers\API\StoreController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['message' => 'Welcome to the API']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// API V1
Route::prefix('v1')->group(function () {
    // PUBLIC
    Route::prefix('itemmaster')->group(function () {
        Route::get('/images/{oxyItemMasterId}', [ItemMasterController::class, 'images']);
        Route::get('/', [ItemMasterController::class, 'getItemMasters']);
        Route::get('/detail', [ItemMasterController::class, 'getItemMasterDetail']);
        Route::get('/prices/{oxyItemMasterId}', [ItemMasterController::class, 'getItemMasterPrice']);
        Route::get('/stock', [ItemMasterController::class, 'getItemMasterStock']);
        Route::get('/stocklocation/{oxyItemMasterId}', [ItemMasterController::class, 'getItemMasterStockLocation']);
    });

    Route::prefix('category')->group(function () {
        Route::get('/images/{oxyCategoryId}', [CategoryController::class, 'images']);
        Route::get('/', [CategoryController::class, 'getCategories']);
        Route::get('/sub', [CategoryController::class, 'getSubCategories']);
    });

    Route::prefix('store')->group(function () {
        Route::get('/images/{oxyStoreId}', [StoreController::class, 'images']);
        Route::get('/locations', [StoreController::class, 'getLocations']);
    });

    // promo
    Route::prefix('promo')->group(function () {
        Route::get('', [PromoController::class, 'index']);
        Route::get('count', [PromoController::class, 'count']);
    });

    // image slider
    Route::prefix('image-slider')->group(function () {
        Route::get('', [ImageSliderController::class, 'index']);
    });

    // PROTECTED
    Route::middleware('oxy.auth')->prefix('protected')->group(function () {
        Route::get('/test', function () {
            return ApiResponse::success(
                data: null,
                message: 'Welcome to the Protected API'
            );
        });
    });
});
