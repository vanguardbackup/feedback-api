<?php

namespace App\Http\Controllers;

use App\Http\Resources\FeedbackResource;
use App\Models\Feedback;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Handles searching and filtering feedback entries.
 */
class SearchFeedbackController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): AnonymousResourceCollection
    {
        $query = Feedback::query();

        $this->applySearchFilters($query, $request);
        $this->applySpecificFilters($query, $request);
        $this->applySorting($query, $request);

        $perPage = (int) $request->input('per_page', 15);
        $feedbacks = $query->paginate($perPage);

        return FeedbackResource::collection($feedbacks);
    }

    /**
     * Apply search filters to the query.
     */
    private function applySearchFilters(Builder $query, Request $request): void
    {
        $searchTerm = $request->input('query');

        if ($searchTerm) {
            $query->where(function (Builder $query) use ($searchTerm) {
                $query->where('experiment', 'like', "%{$searchTerm}%")
                    ->orWhere('feedback', 'like', "%{$searchTerm}%")
                    ->orWhere('php_version', 'like', "%{$searchTerm}%")
                    ->orWhere('vanguard_version', 'like', "%{$searchTerm}%")
                    ->orWhere('email_address', 'like', "%{$searchTerm}%");
            });
        }
    }

    /**
     * Apply specific filters to the query.
     */
    private function applySpecificFilters(Builder $query, Request $request): void
    {
        if ($request->has('experiment')) {
            $query->where('experiment', $request->input('experiment'));
        }

        if ($request->has('php_version')) {
            $query->where('php_version', $request->input('php_version'));
        }

        if ($request->has('vanguard_version')) {
            $query->where('vanguard_version', $request->input('vanguard_version'));
        }

        if ($request->has('has_email')) {
            $hasEmail = filter_var($request->input('has_email'), FILTER_VALIDATE_BOOLEAN);
            $query->when($hasEmail, fn ($q) => $q->whereNotNull('email_address'),
                fn ($q) => $q->whereNull('email_address'));
        }

        if ($request->has('from_date')) {
            $query->where('created_at', '>=', $request->input('from_date'));
        }

        if ($request->has('to_date')) {
            $query->where('created_at', '<=', $request->input('to_date'));
        }
    }

    /**
     * Apply sorting to the query.
     */
    private function applySorting(Builder $query, Request $request): void
    {
        $sortField = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');

        $allowedSortFields = ['created_at', 'experiment', 'php_version', 'vanguard_version'];

        if (in_array($sortField, $allowedSortFields, true)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->latest();
        }
    }
}
