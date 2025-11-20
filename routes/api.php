<?php

use App\Http\Controllers\Api\V1\AuthController;
use Illuminate\Support\Facades\Route;

// Google OAuth callback (direct route without v1 prefix)
Route::get('/google/callback', [AuthController::class, 'googleCallback']);

// Google login direct route
Route::post('/v1/google-login', [AuthController::class, 'googleLogin']);

Route::prefix('v1')->group(function () {
    // Auth routes
    Route::prefix('auth')->group(function () {
        require __DIR__.'/api/v1/auth.php';
    });

    // Tour routes
    Route::prefix('tours')->group(function () {
        require __DIR__.'/api/v1/tours.php';
    });

    // Booking routes
    Route::prefix('bookings')->group(function () {
        require __DIR__.'/api/v1/bookings.php';
    });

    // Review routes
    Route::prefix('reviews')->group(function () {
        require __DIR__.'/api/v1/reviews.php';
    });

    // Favorite routes
    Route::prefix('favorites')->group(function () {
        require __DIR__.'/api/v1/favorites.php';
    });

    // Question routes
    Route::prefix('questions')->group(function () {
        require __DIR__.'/api/v1/questions.php';
    });

    // User routes
    Route::prefix('users')->group(function () {
        require __DIR__.'/api/v1/users.php';
    });

    // Search routes
    Route::prefix('search')->group(function () {
        require __DIR__.'/api/v1/search.php';
    });
});
