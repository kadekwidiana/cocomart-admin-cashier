<?php

use App\Helpers\ApiResponse;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\ImageSliderController;
use App\Http\Controllers\API\ItemMasterController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\PromoController;
use App\Http\Controllers\API\LocationController;
use App\Http\Controllers\API\ShipmentController;
use App\Http\Controllers\API\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// -- FALLBACK --
Route::fallback(function (Request $request) {
    return ApiResponse::error(
        [
            'detail' => 'The requested route was not found.',
        ],
        'Resource Not Found',
        404
    );
});

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
        Route::get('/detail-with-images', [ItemMasterController::class, 'getItemMasterDetailWithImages']);
        Route::get('/prices/{oxyItemMasterId}', [ItemMasterController::class, 'getItemMasterPrice']);
        Route::get('/stock', [ItemMasterController::class, 'getItemMasterStock']);
        Route::get('/stocklocation/{oxyItemMasterId}', [ItemMasterController::class, 'getItemMasterStockLocation']);
    });

    Route::prefix('category')->group(function () {
        Route::get('/images/{oxyCategoryId}', [CategoryController::class, 'images']);
        Route::get('/', [CategoryController::class, 'getCategories']);
        Route::get('/with-images', [CategoryController::class, 'getCategoriesWithImages']);
        Route::get('/sub', [CategoryController::class, 'getSubCategories']);
    });

    Route::prefix('location')->group(function () {
        Route::get('', [LocationController::class, 'getLocations']);
        Route::get('/with-images', [LocationController::class, 'getLocationsWithImages']);
        Route::get('/images/{oxyLocationId}', [LocationController::class, 'images']);
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

    // shipment webhook (dipanggil oleh Grab, tanpa oxy auth)
    Route::post('/shipment/webhook', [ShipmentController::class, 'webhook']);

    // PROTECTED
    Route::middleware('oxy.auth')->prefix('protected')->group(function () {
        Route::get('/test', function () {
            return ApiResponse::success(
                data: null,
                message: 'Welcome to the Protected API'
            );
        });

        // notification
        Route::prefix('notification')->group(function () {
            Route::get('/customer/{oxyCustomerId}', [NotificationController::class, 'index']);
            Route::get('count', [NotificationController::class, 'count']);
            Route::post('read', [NotificationController::class, 'read']);
        });

        // item master
        Route::prefix('itemmaster')->group(function () {
            Route::get('/check-is-wishlist/{oxyItemMasterId}/customer/{oxyCustomerId}', [ItemMasterController::class, 'checkIsWishlist']);
            Route::post('/add-to-wishlist', [ItemMasterController::class, 'addToWishList']);
            Route::delete('/remove-from-wishlist/{oxyItemMasterId}/customer/{oxyCustomerId}', [ItemMasterController::class, 'removeFromWishList']);
            Route::get('/wishlist-by-customer/{oxyCustomerId}/location/{oxyLocationId}', [ItemMasterController::class, 'getItemMasterWishlistByCustomer']);
            Route::post('/by-ids', [ItemMasterController::class, 'getItemMasterByIds']);
            Route::get('/detail-with-images-and-is-wishlist/{oxyCustomerId}', [ItemMasterController::class, 'getItemMasterDetailWithImagesAndIsWishlist']);
        });

        // transaction
        Route::prefix('transaction')->group(function () {
            Route::get('/list/{oxyCustomerId}', [TransactionController::class, 'index']);
            Route::get('/{transactionId}', [TransactionController::class, 'showSimple']);
            Route::get('/detail/{transactionId}', [TransactionController::class, 'showDetail']);
            Route::post('', [TransactionController::class, 'store']);
            Route::post('/{transactionId}/pay', [TransactionController::class, 'pay']);
        });

        // shipment (Grab)
        Route::prefix('shipment')->group(function () {
            Route::post('/quote', [ShipmentController::class, 'quote']);
            Route::get('/{deliveryId}', [ShipmentController::class, 'show']);
            Route::delete('/{deliveryId}', [ShipmentController::class, 'cancel']);
        });
    });
});
