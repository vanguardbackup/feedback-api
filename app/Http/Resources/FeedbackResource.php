<?php

namespace App\Http\Resources;

use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource for transforming Feedback models into API responses.
 */
class FeedbackResource extends JsonResource
{
    /** @var Feedback */
    public $resource;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'experiment' => $this->resource->experiment,
            'feedback' => $this->resource->feedback,
            'feedback_summary' => $this->resource->getSummary(100),
            'php_version' => $this->resource->php_version,
            'vanguard_version' => $this->resource->vanguard_version,
            'email_address' => $this->when($this->resource->hasEmailAddress(), $this->resource->email_address),
            'email_domain' => $this->when($this->resource->hasEmailAddress(), $this->resource->getEmailDomain()),
            'has_email' => $this->resource->hasEmailAddress(),
            'age_in_days' => $this->resource->getAgeInDays(),
            'created_at' => $this->resource->created_at->toIso8601String(),
            'updated_at' => $this->resource->updated_at->toIso8601String(),
        ];
    }
}
