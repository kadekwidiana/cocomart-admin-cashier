<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ImageSliderController;
use App\Http\Controllers\ItemMasterController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Catch-all route to handle 404 errors
Route::fallback(function () {
    return Inertia::render('Error/Error', ['status' => 404])
        ->toResponse(request())
        ->setStatusCode(404);
});

// redirect to login page
Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Backpage/Dashboard/Index', [
            'title' => 'Dashboard',
        ]);
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/{userId}', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('checkRole:ADMIN')->group(function () {
        // image slider
        Route::resource('image-sliders', ImageSliderController::class);
        Route::post('image-sliders/{id}/update', [ImageSliderController::class, 'update'])->name('image-sliders.update');

        // promo
        Route::resource('promos', PromoController::class);
        Route::post('promos/{id}/update', [PromoController::class, 'update'])->name('promos.update');

        // notification
        Route::resource('notifications', NotificationController::class);
        Route::post('notifications/{id}/update', [NotificationController::class, 'update'])->name('notifications.update');

        // user
        Route::resource('users', UserController::class);
        Route::post('users/{id}/update', [UserController::class, 'update'])->name('users.update');
    });

    // category
    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index')->middleware('checkRole:ADMIN');
    Route::get('categories/{code}', [CategoryController::class, 'show'])->name('categories.show');
    Route::post('categories/image/add/{oxyCategoryId}', [CategoryController::class, 'addImage'])->name('categories.addImage')->middleware('checkRole:ADMIN');
    Route::delete('categories/image/delete/{id}', [CategoryController::class, 'deleteImage'])->name('categories.deleteImage')->middleware('checkRole:ADMIN');

    // location
    Route::get('locations', [LocationController::class, 'index'])->name('locations.index')->middleware('checkRole:ADMIN');
    Route::get('locations/{code}', [LocationController::class, 'show'])->name('locations.show');
    Route::post('locations/image/add/{oxyLocationId}', [LocationController::class, 'addImage'])->name('locations.addImage')->middleware('checkRole:ADMIN');
    Route::delete('locations/image/delete/{id}', [LocationController::class, 'deleteImage'])->name('locations.deleteImage')->middleware('checkRole:ADMIN');

    // item master
    Route::get('item-masters', [ItemMasterController::class, 'index'])->name('item-masters.index')->middleware('checkRole:ADMIN');
    Route::get('item-masters/{itemMasterId}', [ItemMasterController::class, 'show'])->name('item-masters.show');
    Route::post('item-masters/image/add/{oxyItemMasterId}', [ItemMasterController::class, 'addImage'])->name('item-masters.addImage')->middleware('checkRole:ADMIN');
    Route::delete('item-masters/image/delete/{id}', [ItemMasterController::class, 'deleteImage'])->name('item-masters.deleteImage')->middleware('checkRole:ADMIN');

    // transaction
    Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('transactions/{id}', [TransactionController::class, 'show'])->name('transactions.show');
    Route::patch('/transactions/{id}/status', [TransactionController::class, 'updateStatus']);
    Route::patch('/transactions/{id}/pickup-status', [TransactionController::class, 'updatePickupStatus']);
    Route::patch('/transactions/{id}/shipment-status', [TransactionController::class, 'updateShipmentStatus']);
});

require __DIR__ . '/auth.php';


// storage link dan cronjob tidak bisa, jadi pake cara ini
// Route::get('/storage/{folder}/{filename}', function ($folder, $filename) {
//     $allowedFolders = ['image-sliders', 'promos', 'notifications', 'categories', 'locations', 'item-masters'];

//     if (!in_array($folder, $allowedFolders)) {
//         abort(404);
//     }

//     $path = storage_path("app/public/{$folder}/{$filename}");

//     if (!file_exists($path)) {
//         abort(404);
//     }

//     return response()->file($path);
// });
