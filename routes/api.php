<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\Api\CarController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\FlightController;


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
    //  Car routes
    Route::prefix('cars')->group(function () {
require __DIR__.'/api/v1/cars.php';
    });

    // Compare routes (unified for all categories)
    Route::get('/compare/search', [\App\Http\Controllers\Api\V1\CompareController::class, 'search']);
    Route::post('/compare', [\App\Http\Controllers\Api\V1\CompareController::class, 'compare']);
});
Route::middleware('auth:sanctum')->group(function () {
    // flights
    Route::get('/flights', [FlightController::class, 'index']);   //show all flights
    Route::get('/flights/{id}', [FlightController::class, 'show']);       //show one flight
    Route::post('/flights/search', [FlightController::class, 'search']);  //search for flights
    Route::get('/{flightId}/seats', [FlightController::class, 'seatAvailability']); //flight seats
    // Route::apiResource('bookings', BookingController::class);
    // Route::post('bookings/{booking}/confirm', [BookingController::class, 'confirmBooking']);
    // Route::post('bookings/{booking}/cancel', [BookingController::class, 'cancelBooking']);
});
