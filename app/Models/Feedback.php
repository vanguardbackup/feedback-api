<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Class Feedback
 *
 *
 * @property int $id
 * @property string $experiment
 * @property string $feedback
 * @property string $php_version
 * @property string $vanguard_version
 * @property string|null $email_address
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @method static Builder|Feedback whereExperiment(string $experiment)
 * @method static Builder|Feedback wherePhpVersion(string $phpVersion)
 * @method static Builder|Feedback whereVanguardVersion(string $vanguardVersion)
 * @method static Builder|Feedback whereEmailAddress(string $emailAddress)
 */
class Feedback extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope a query to only include feedback for a specific experiment.
     */
    public function scopeForExperiment(Builder $query, string $experiment): Builder
    {
        return $query->where('experiment', $experiment);
    }

    /**
     * Scope a query to only include feedback for a specific PHP version.
     */
    public function scopeForPhpVersion(Builder $query, string $version): Builder
    {
        return $query->where('php_version', $version);
    }

    /**
     * Scope a query to only include feedback for a specific Vanguard version.
     */
    public function scopeForVanguardVersion(Builder $query, string $version): Builder
    {
        return $query->where('vanguard_version', $version);
    }

    /**
     * Scope a query to only include recent feedback.
     */
    public function scopeRecent(Builder $query, int $days = 7): Builder
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays($days));
    }

    /**
     * Scope a query to only include feedback with an email address.
     */
    public function scopeWithEmail(Builder $query): Builder
    {
        return $query->whereNotNull('email_address');
    }

    /**
     * Scope a query to only include feedback without an email address.
     */
    public function scopeWithoutEmail(Builder $query): Builder
    {
        return $query->whereNull('email_address');
    }

    /**
     * Get a summary of the feedback.
     */
    public function getSummary(int $length = 100): string
    {
        return Str::limit($this->feedback, $length);
    }

    /**
     * Check if the feedback is for a specific experiment.
     */
    public function isForExperiment(string $experiment): bool
    {
        return $this->experiment === $experiment;
    }

    /**
     * Check if the feedback has an associated email address.
     */
    public function hasEmailAddress(): bool
    {
        return ! is_null($this->email_address);
    }

    /**
     * Get the domain of the email address, if present.
     */
    public function getEmailDomain(): ?string
    {
        if (! $this->hasEmailAddress()) {
            return null;
        }

        return Str::after($this->email_address, '@');
    }

    /**
     * Scope a query to only include feedback from a specific email domain.
     */
    public function scopeFromEmailDomain(Builder $query, string $domain): Builder
    {
        return $query->where('email_address', 'like', '%@' . $domain);
    }

    /**
     * Get the feedback age in days.
     */
    public function getAgeInDays(): int
    {
        return $this->created_at->diffInDays();
    }

    /**
     * Check if the feedback is older than a given number of days.
     */
    public function isOlderThan(int $days): bool
    {
        return $this->getAgeInDays() > $days;
    }
}
