<?php

use App\Http\Controllers\ProcessFeedbackController;
use App\Http\Controllers\SearchFeedbackController;
use App\Http\Controllers\ViewFeedbackController;
use Illuminate\Support\Facades\Route;

Route::post('feedback', ProcessFeedbackController::class)->middleware('throttle:5,1');

Route::get('feedback', ViewFeedbackController::class)->middleware('api.key');

Route::get('feedback/search', SearchFeedbackController::class)->middleware('api.key');
