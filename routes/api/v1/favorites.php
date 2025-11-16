<?php

use App\Http\Controllers\Api\V1\FavoriteController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/', [FavoriteController::class, 'index']);
    Route::post('/{tourId}/toggle', [FavoriteController::class, 'toggle']);
    Route::get('/{tourId}/check', [FavoriteController::class, 'check']);
});

