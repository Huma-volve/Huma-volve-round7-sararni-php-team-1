<?php

use App\Http\Controllers\Api\V1\AuthController;
use Illuminate\Support\Facades\Route;

// Public auth routes
Route::post('/register', [AuthController::class, 'register']);

// OTP routes with rate limiting
Route::middleware('throttle:5,1')->group(function () {
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
});

Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->middleware('throttle:3,1');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->middleware('throttle:5,1');

// Google OAuth routes
Route::get('/google/url', [AuthController::class, 'getGoogleAuthUrl']);
Route::post('/google/exchange', [AuthController::class, 'exchangeGoogleCode']);

// Protected auth routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);

    // Google OAuth protected routes
    Route::post('/google/link', [AuthController::class, 'linkGoogleAccount']);
    Route::post('/google/unlink', [AuthController::class, 'unlinkGoogleAccount']);
    Route::get('/providers', [AuthController::class, 'getProviders']);
});

