<?php

use App\Http\Controllers\ProcessFeedbackController;
use App\Http\Controllers\SearchFeedbackController;
use App\Http\Controllers\ViewFeedbackController;
use Illuminate\Support\Facades\Route;

Route::prefix('feedback')->group(function () {
    // Public route for submitting feedback, rate limited
    Route::post('/', ProcessFeedbackController::class)
        ->middleware('throttle:submissions')
        ->name('feedback.submit');

    // Protected routes for viewing and searching feedback
    Route::middleware('api.key')->group(function () {
        Route::get('/', ViewFeedbackController::class)
            ->name('feedback.view');
        Route::get('search', SearchFeedbackController::class)
            ->name('feedback.search');
    });
});
