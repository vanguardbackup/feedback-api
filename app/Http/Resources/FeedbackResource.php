<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeedbackResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'experiment' => $this->experiment,
            'feedback' => $this->feedback,
            'feedback_summary' => $this->getSummary(100),
            'php_version' => $this->php_version,
            'vanguard_version' => $this->vanguard_version,
            'email_address' => $this->when($this->email_address, $this->email_address),
            'email_domain' => $this->when($this->email_address, $this->getEmailDomain()),
            'has_email' => $this->hasEmailAddress(),
            'age_in_days' => $this->getAgeInDays(),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
