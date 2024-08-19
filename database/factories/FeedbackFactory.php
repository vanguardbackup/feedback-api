<?php

namespace Database\Factories;

use App\Models\Feedback;
use DateTime;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class FeedbackFactory
 *
 * @extends Factory<Feedback>
 */
class FeedbackFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'experiment' => fake()->word(),
            'feedback' => fake()->paragraph(),
            'php_version' => fake()->semver(),
            'vanguard_version' => fake()->semver(),
            'email_address' => fake()->optional()->safeEmail(),
            'created_at' => fake()->dateTimeThisYear(),
            'updated_at' => fake()->dateTimeThisYear(),
        ];
    }

    /**
     * Set the PHP version for the feedback.
     */
    public function withPhpVersion(string $version): self
    {
        return $this->state(fn (array $attributes): array => [
            'php_version' => $version,
        ]);
    }

    /**
     * Set the Vanguard version for the feedback.
     */
    public function withVanguardVersion(string $version): self
    {
        return $this->state(fn (array $attributes): array => [
            'vanguard_version' => $version,
        ]);
    }

    /**
     * Set the experiment name for the feedback.
     */
    public function forExperiment(string $experiment): self
    {
        return $this->state(fn (array $attributes): array => [
            'experiment' => $experiment,
        ]);
    }

    /**
     * Create feedback without an email address.
     */
    public function withoutEmail(): self
    {
        return $this->state(fn (array $attributes): array => [
            'email_address' => null,
        ]);
    }

    /**
     * Create feedback with a specific email address.
     */
    public function withEmail(?string $email = null): self
    {
        return $this->state(fn (array $attributes): array => [
            'email_address' => $email ?? fake()->safeEmail(),
        ]);
    }

    /**
     * Create feedback from a specific date.
     */
    public function fromDate(DateTime $date): self
    {
        return $this->state(fn (array $attributes): array => [
            'created_at' => $date,
            'updated_at' => $date,
        ]);
    }
}
