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

        $perPage = $this->getPerPage($request);
        $feedbacks = $query->paginate($perPage);

        return FeedbackResource::collection($feedbacks);
    }

    /**
     * Apply search filters to the query.
     *
     * @param  Builder<Feedback>  $query
     */
    private function applySearchFilters(Builder $query, Request $request): void
    {
        $searchTerm = $request->input('query');

        if (! is_string($searchTerm) || $searchTerm === '') {
            return;
        }

        $query->where(function (Builder $query) use ($searchTerm) {
            $query->where('experiment', 'like', "%{$searchTerm}%")
                ->orWhere('feedback', 'like', "%{$searchTerm}%")
                ->orWhere('php_version', 'like', "%{$searchTerm}%")
                ->orWhere('vanguard_version', 'like', "%{$searchTerm}%")
                ->orWhere('email_address', 'like', "%{$searchTerm}%");
        });
    }

    /**
     * Apply specific filters to the query.
     *
     * @param  Builder<Feedback>  $query
     */
    private function applySpecificFilters(Builder $query, Request $request): void
    {
        $filters = [
            'experiment' => fn ($value) => $query->where('experiment', $value),
            'php_version' => fn ($value) => $query->where('php_version', $value),
            'vanguard_version' => fn ($value) => $query->where('vanguard_version', $value),
            'has_email' => fn ($value) => $this->applyEmailFilter($query, $value),
            'from_date' => fn ($value) => $query->where('created_at', '>=', $value),
            'to_date' => fn ($value) => $query->where('created_at', '<=', $value),
        ];

        foreach ($filters as $key => $filter) {
            if ($request->has($key)) {
                $filter($request->input($key));
            }
        }
    }

    /**
     * Apply email filter to the query.
     *
     * @param  Builder<Feedback>  $query
     */
    private function applyEmailFilter(Builder $query, mixed $value): void
    {
        $hasEmail = filter_var($value, FILTER_VALIDATE_BOOLEAN);
        $query->when($hasEmail,
            fn (Builder $q) => $q->whereNotNull('email_address'),
            fn (Builder $q) => $q->whereNull('email_address')
        );
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

        if (! is_string($sortField) || ! in_array($sortField, $allowedSortFields, true)) {
            $query->latest();

            return;
        }

        $query->orderBy($sortField, $sortDirection);
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
