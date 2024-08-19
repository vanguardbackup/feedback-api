<?php

namespace App\Http\Controllers;

use App\Http\Requests\FeedbackRequest;
use App\Models\Feedback;
use Illuminate\Http\JsonResponse;

class ProcessFeedbackController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(FeedbackRequest $request): JsonResponse
    {
        Feedback::create($request->validated());

        return response()->json([
            'message' => 'Experiment feedback successfully submitted',
            'status' => 'success',
        ], 201);
    }
}
