<?php

use App\Http\Controllers\Api\CarController;
use App\Http\Controllers\Api\V1\CarPaymentController;
use App\Http\Controllers\BrandController;
use Illuminate\Support\Facades\Route;

Route::prefix('brands')->group(function () {
    Route::get('/', [BrandController::class, 'index']); // getAllBrands -> /api/v1/cars/brands/
    Route::post('/storeNewBrand', [BrandController::class,'store']); //storeNewBrand ->/api/v1/cars/brands/storeNewBrand
});

Route::get('/booking', [CarController::class, 'booking_car']);
Route::get('/', [CarController::class, 'index']); //showAllCar -> /api/v1/cars
Route::get('/{id}', [CarController::class, 'show']); // showOneByCarId ->/api/v1/cars/30 
Route::get('/showByBrandID/{brand_id}', [CarController::class, 'showByBrandID']); //ShowByBrandID ->/api/v1/cars/showByBrandID/1
Route::post('/availability/{car_id}', [CarController::class, 'availability']);


Route::post('/payment/process', [CarPaymentController::class, 'paymentProcess']);
Route::match(['GET','POST'],'/payment/callback', [CarPaymentController::class, 'callBack']);

// Route::middleware(['role:admin'])->group(function () {
    Route::post('/', [CarController::class, 'store']); //StoreNewCar -> /api/v1/cars 
    Route::put('/{id}', [CarController::class, 'update']);
    Route::delete('/{id}', [CarController::class, 'destroy']);
// });

