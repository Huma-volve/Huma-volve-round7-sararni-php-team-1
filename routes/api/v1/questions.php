<?php

use App\Http\Controllers\Api\V1\QuestionController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/', [QuestionController::class, 'store']);
    Route::post('/{id}/answer', [QuestionController::class, 'answer']);
});
