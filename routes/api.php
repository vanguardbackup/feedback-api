<?php

use App\Http\Controllers\ProcessFeedbackController;
use App\Http\Controllers\SearchFeedbackController;
use App\Http\Controllers\ViewFeedbackController;
use Illuminate\Support\Facades\Route;

Route::prefix('feedback')->group(function () {
    Route::post('/', ProcessFeedbackController::class)->middleware('throttle:5,1');

    Route::middleware('api.key')->group(function () {
        Route::get('/', ViewFeedbackController::class);
        Route::get('search', SearchFeedbackController::class);
    });
});
