<?php

use App\Http\Controllers\Api\V1\BookingController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    // Booking utility routes with rate limiting
    Route::middleware('throttle:10,1')->group(function () {
        Route::post('/check-availability', [BookingController::class, 'checkAvailability']);
        Route::post('/calculate-price', [BookingController::class, 'calculatePrice']);
    });

    // Booking CRUD routes
    Route::post('/', [BookingController::class, 'store']);
    Route::get('/', [BookingController::class, 'index']);
    Route::get('/{id}', [BookingController::class, 'show']);
    Route::post('/{id}/confirm', [BookingController::class, 'confirm']);
    Route::post('/{id}/cancel', [BookingController::class, 'cancel']);
});

