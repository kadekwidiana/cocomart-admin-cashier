<?php

use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\ItemMasterController;
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
    Route::prefix('item-master')->group(function () {
        Route::get('/images/{oxy_item_master_id}', [ItemMasterController::class, 'images']);
    });

    Route::prefix('category')->group(function () {
        Route::get('/images/{oxy_category_id}', [CategoryController::class, 'images']);
    });

    Route::prefix('store')->group(function () {
        Route::get('/images/{oxy_store_id}', [StoreController::class, 'images']);
    });
});
