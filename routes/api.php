<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CompareController;

use App\Http\Controllers\Api\V1\FlightBookingController;

use App\Http\Controllers\Api\V1\FlightController;
use App\Http\Controllers\BrandController;
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
    //  Car routes
    Route::prefix('cars')->group(function () {
        require __DIR__.'/api/v1/cars.php';
    });

    // Compare routes (unified for all categories)
    Route::get('/compare/search', [\App\Http\Controllers\Api\V1\CompareController::class, 'search']);
    Route::post('/compare', [\App\Http\Controllers\Api\V1\CompareController::class, 'compare']);
});

Route::middleware('auth:sanctum')->prefix('flights')->group(function () {
    
    Route::get('/', [FlightController::class, 'index']);   //show all flights
    Route::get('/{id}', [FlightController::class, 'show']);       //show one flight
    Route::post('/search', [FlightController::class, 'search']);  //search for flights
    Route::get('/{flightId}/seats', [FlightController::class, 'seatAvailability']); //flight seats
    Route::get('/userBookings/{id}', [FlightBookingController::class,'userBookings']); //show user flights
   Route::post('/book', [FlightBookingController::class, 'bookFlight']);//booking
   Route::get('show/{id}', [FlightBookingController::class, 'show']);//show specific booking details
 

    Route::prefix('brands')->group(function () {
        Route::get('/', [BrandController::class, 'index']);
    });

});
