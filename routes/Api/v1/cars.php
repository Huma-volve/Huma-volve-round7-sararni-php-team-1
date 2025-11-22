<?php

use App\Http\Controllers\Api\CarController;
use App\Http\Controllers\BrandController;
use Illuminate\Support\Facades\Route;

Route::get('/cars', [CarController::class, 'index']);
Route::get('/cars/{id}', [CarController::class, 'show']);
Route::get('/cars/showByBrandID/{brand_id}', [CarController::class, 'showByBrandID']);
Route::get('/brands', [BrandController::class, 'index']);


Route::middleware(['role:admin'])->group(function () {
    Route::post('/cars', [CarController::class, 'store']);
    Route::put('/cars/{id}', [CarController::class, 'update']);
    Route::delete('/cars/{id}', [CarController::class, 'destroy']);
});