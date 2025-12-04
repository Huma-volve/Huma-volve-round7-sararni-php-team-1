<?php

use App\Http\Controllers\Api\Hotel\BookingController;
use App\Http\Controllers\Api\Hotel\HotelController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\Hotel\RoomController;
use App\Models\Booking;
use App\Services\Payment\StripeProvider;
use Illuminate\Support\Facades\Route;




Route::prefix('hotels')->group(function (){
    // -------------------- Hotels --------------------
Route::get('/', [HotelController::class, 'getAllHotel']);
Route::get('/{hotel}', [HotelController::class, 'show']);

// -------------------- Rooms inside Hotel --------------------
Route::get('/{hotel}/rooms', [RoomController::class, 'index']);
Route::get('/{hotel}/rooms/{room}', [RoomController::class, 'show']);

});



Route::prefix('bookings')->group(function (){
    // -------------------- Bookings Hotels --------------------

Route::post('/hotel', [BookingController::class, 'store']);
Route::post('/{id}/confirm', [BookingController::class, 'confirm']);
Route::post('/{id}/cancel', [BookingController::class, 'cancel']);

});





