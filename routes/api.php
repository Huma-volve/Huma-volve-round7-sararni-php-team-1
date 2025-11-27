<?php

use App\Http\Controllers\Api\CarController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\StripePaymentController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\BrandController;


use Illuminate\Support\Facades\Route;


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
});





Route::controller(PaymentController::class)->prefix('payments')->group(function () {

Route::post('/create', 'createPayment');
Route::get('/success','paymentSuccess')->name('payment.success');
Route::get('/cancel','cancel')->name('payment.cancel');
// Route::post('/paypal/webhook', [PaymentController::class, 'webhook']);
});

require __DIR__ . '/Api/hotel.php';

