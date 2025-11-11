<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

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
    });
});
