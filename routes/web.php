<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ImageSliderController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PromoController;
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

    // image slider
    Route::resource('image-sliders', ImageSliderController::class);
    Route::post('image-sliders/{id}/update', [ImageSliderController::class, 'update'])->name('image-sliders.update');

    // promo
    Route::resource('promos', PromoController::class);
    Route::post('promos/{id}/update', [PromoController::class, 'update'])->name('promos.update');

    // notification
    Route::resource('notifications', NotificationController::class);
    Route::post('notifications/{id}/update', [NotificationController::class, 'update'])->name('notifications.update');

    // category
    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('categories/{code}', [CategoryController::class, 'show'])->name('categories.show');
    Route::post('categories/image/add/{oxyCategoryId}', [CategoryController::class, 'addImage'])->name('categories.addImage');
    Route::delete('categories/image/delete/{id}', [CategoryController::class, 'deleteImage'])->name('categories.deleteImage');

    // location
    Route::get('locations', [LocationController::class, 'index'])->name('locations.index');
    Route::get('locations/{code}', [LocationController::class, 'show'])->name('locations.show');
    Route::post('locations/image/add/{oxyLocationId}', [LocationController::class, 'addImage'])->name('locations.addImage');
    Route::delete('locations/image/delete/{id}', [LocationController::class, 'deleteImage'])->name('locations.deleteImage');
});

require __DIR__ . '/auth.php';


// storage link dan cronjob tidak bisa, jadi pake cara ini
Route::get('/storage/{folder}/{filename}', function ($folder, $filename) {
    $allowedFolders = ['image-sliders', 'promos', 'notifications', 'categories', 'locations'];

    if (!in_array($folder, $allowedFolders)) {
        abort(404);
    }

    $path = storage_path("app/public/{$folder}/{$filename}");

    if (!file_exists($path)) {
        abort(404);
    }

    return response()->file($path);
});
