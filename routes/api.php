<?php


use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\UserController;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\FlightController;


Route::prefix('v1')->group(function () {

    // Public auth routes
    Route::post('/auth/register', [AuthController::class, 'register']);

    // OTP routes with rate limiting
    Route::middleware('throttle:5,1')->group(function () {
        Route::post('/auth/verify-otp', [AuthController::class, 'verifyOtp']);
        Route::post('/auth/resend-otp', [AuthController::class, 'resendOtp']);
    });

    Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword'])->middleware('throttle:3,1');
    Route::post('/auth/reset-password', [AuthController::class, 'resetPassword'])->middleware('throttle:5,1');

    // Google OAuth routes
    Route::get('/auth/google/url', [AuthController::class, 'getGoogleAuthUrl']);
    Route::post('/auth/google/exchange', [AuthController::class, 'exchangeGoogleCode']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        // Auth routes
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::post('/auth/change-password', [AuthController::class, 'changePassword']);

        // Google OAuth protected routes
        Route::post('/auth/google/link', [AuthController::class, 'linkGoogleAccount']);
        Route::post('/auth/google/unlink', [AuthController::class, 'unlinkGoogleAccount']);
        Route::get('/auth/providers', [AuthController::class, 'getProviders']);

        // User routes
        Route::get('/users/me', [UserController::class, 'me']);
        Route::put('/users/me', [UserController::class, 'updateProfile']);

        // Auth routes
        Route::prefix('auth')->group(function () {
            require __DIR__ . '/api/v1/auth.php';
        });

        // Tour routes
        Route::prefix('tours')->group(function () {
            require __DIR__ . '/api/v1/tours.php';
        });

        // Booking routes
        Route::prefix('bookings')->group(function () {
            require __DIR__ . '/api/v1/bookings.php';
        });

        // Review routes
        Route::prefix('reviews')->group(function () {
            require __DIR__ . '/api/v1/reviews.php';
        });

        // Favorite routes
        Route::prefix('favorites')->group(function () {
            require __DIR__ . '/api/v1/favorites.php';
        });

        // Question routes
        Route::prefix('questions')->group(function () {
            require __DIR__ . '/api/v1/questions.php';
        });

        // User routes
        Route::prefix('users')->group(function () {
            require __DIR__ . '/api/v1/users.php';
        });

        // Search routes
        Route::prefix('search')->group(function () {
            require __DIR__ . '/api/v1/search.php';
        });
    });
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
