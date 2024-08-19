<?php

namespace App\Http\Controllers;

use App\Http\Requests\FeedbackRequest;
use App\Models\Feedback;
use ConsoleTVs\Profanity\Facades\Profanity;
use Illuminate\Http\JsonResponse;

class ProcessFeedbackController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(FeedbackRequest $request): JsonResponse
    {
        Feedback::create([
            'experiment' => $request->get('experiment'),
            'feedback' => Profanity::blocker($request->get('feedback'))->filter(),
            'php_version' => $request->get('php_version'),
            'vanguard_version' => $request->get('vanguard_version'),
            'email_address' => $request->get('email_address'),
        ]);

        return response()->json([
            'message' => 'Experiment feedback successfully submitted',
            'status' => 'success',
        ], 201);
    }
}
