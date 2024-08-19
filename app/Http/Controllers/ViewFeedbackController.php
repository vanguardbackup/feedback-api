<?php

namespace App\Http\Controllers;

use App\Http\Resources\FeedbackResource;
use App\Models\Feedback;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Handles retrieval and filtering of feedback entries.
 */
class ViewFeedbackController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): AnonymousResourceCollection
    {
        $query = Feedback::query();

        $this->applyFilters($query, $request);
        $this->applySorting($query, $request);

        $perPage = (int) $request->input('per_page', 15);
        $feedbacks = $query->paginate($perPage);

        return FeedbackResource::collection($feedbacks);
    }

    /**
     * Apply filters to the query based on request parameters.
     */
    private function applyFilters(Builder $query, Request $request): void
    {
        $filters = [
            'experiment',
            'php_version',
            'vanguard_version',
            'email_address',
        ];

        foreach ($filters as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->input($filter));
            }
        }

        if ($request->filled('has_email')) {
            $hasEmail = filter_var($request->input('has_email'), FILTER_VALIDATE_BOOLEAN);
            $query->when($hasEmail, fn ($q) => $q->whereNotNull('email_address'),
                fn ($q) => $q->whereNull('email_address'));
        }

        if ($request->filled('from_date')) {
            $query->where('created_at', '>=', $request->input('from_date'));
        }

        if ($request->filled('to_date')) {
            $query->where('created_at', '<=', $request->input('to_date'));
        }

        if ($request->filled('email_domain')) {
            $query->where('email_address', 'like', '%@' . $request->input('email_domain'));
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
