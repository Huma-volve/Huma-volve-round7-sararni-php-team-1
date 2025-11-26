<?php

use App\Http\Controllers\Api\V1\QuestionController;
use App\Http\Controllers\Api\V1\ReviewController;
use App\Http\Controllers\Api\V1\TourController;
use Illuminate\Support\Facades\Route;

// Public tour routes
Route::get('/', [TourController::class, 'index']);
Route::get('/featured', [TourController::class, 'featured']);
Route::get('/search', [TourController::class, 'search']);
Route::post('/compare', [TourController::class, 'compare']);
Route::get('/{id}', [TourController::class, 'show']);
Route::get('/{id}/similar', [TourController::class, 'similar']);

// Public tour-related routes
Route::get('/{id}/reviews', [ReviewController::class, 'index']);
Route::get('/{id}/questions', [QuestionController::class, 'index']);

