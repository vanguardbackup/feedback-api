<?php

namespace App\Http\Controllers;

use App\Http\Requests\FeedbackRequest;
use App\Models\Feedback;
use ConsoleTVs\Profanity\Facades\Profanity;
use Illuminate\Http\JsonResponse;

/**
 * ProcessFeedbackController handles the submission of user feedback.
 *
 * This controller is responsible for processing and storing user feedback
 * about experiments. It applies profanity filtering and rate limiting.
 */
class ProcessFeedbackController extends Controller
{
    /**
     * Handle the incoming feedback request.
     *
     * This method creates a new Feedback record with the provided data,
     * applying profanity filtering to the feedback text.
     *
     * @param  FeedbackRequest  $request  The validated feedback request
     * @return JsonResponse A JSON response indicating success
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
