<?php

use App\Http\Controllers\Api\V1\SearchController;
use Illuminate\Support\Facades\Route;

// Search routes (public)
Route::get('/', [SearchController::class, 'search']);
Route::get('/quick', [SearchController::class, 'quickSearch']);
Route::get('/nearby', [SearchController::class, 'nearby']);

