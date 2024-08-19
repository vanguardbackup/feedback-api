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

        $perPage = $this->getPerPage($request);
        $feedbacks = $query->paginate($perPage);

        return FeedbackResource::collection($feedbacks);
    }

    /**
     * Apply filters to the query based on request parameters.
     *
     * @param  Builder<Feedback>  $query
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
            $query->when($hasEmail, fn (Builder $q) => $q->whereNotNull('email_address'),
                fn (Builder $q) => $q->whereNull('email_address'));
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
     *
     * @param  Builder<Feedback>  $query
     */
    private function applySorting(Builder $query, Request $request): void
    {
        $sortField = $request->input('sort_by', 'created_at');
        $sortDirection = $this->getSortDirection($request);

        $allowedSortFields = ['created_at', 'experiment', 'php_version', 'vanguard_version'];

        if (is_string($sortField) && in_array($sortField, $allowedSortFields, true)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->latest();
        }
    }

    /**
     * Get the number of items per page.
     */
    private function getPerPage(Request $request): int
    {
        $perPage = $request->input('per_page');

        return is_numeric($perPage) ? max(1, min(100, (int) $perPage)) : 15;
    }

    /**
     * Get the sort direction.
     */
    private function getSortDirection(Request $request): string
    {
        $direction = $request->input('sort_direction');
        if (! is_string($direction)) {
            return 'desc';
        }
        $direction = strtolower($direction);

        return in_array($direction, ['asc', 'desc'], true) ? $direction : 'desc';
    }
}
