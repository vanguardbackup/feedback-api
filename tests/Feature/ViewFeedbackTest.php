<?php

use App\Models\Feedback;
use Illuminate\Support\Facades\Config;

beforeEach(function () {
    Config::set('app.api_key', 'test-api-key');
    $this->apiKey = config('app.api_key');
});

it('retrieves paginated feedback with valid API key', function () {
    Feedback::factory()->withEmail()->count(30)->create();

    $response = $this->withHeaders(['X-API-Key' => $this->apiKey])
        ->getJson('/api/feedback');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'experiment',
                    'feedback',
                    'feedback_summary',
                    'php_version',
                    'vanguard_version',
                    'email_address',
                    'email_domain',
                    'has_email',
                    'age_in_days',
                    'created_at',
                ],
            ],
            'links',
            'meta',
        ])
        ->assertJsonCount(15, 'data');

    $response = $this->withHeaders(['X-API-Key' => $this->apiKey])
        ->getJson('/api/feedback?per_page=20');

    $response->assertStatus(200)
        ->assertJsonCount(20, 'data');
});

it('filters feedback by experiment', function () {
    Feedback::factory()->create(['experiment' => 'TestExperiment']);
    Feedback::factory()->create(['experiment' => 'OtherExperiment']);

    $response = $this->withHeaders(['X-API-Key' => $this->apiKey])
        ->getJson('/api/feedback?experiment=TestExperiment');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.experiment', 'TestExperiment');
});

it('returns unauthorized without API key', function () {
    $response = $this->getJson('/api/feedback');

    $response->assertStatus(401);
});

it('filters feedback by email presence', function () {
    Feedback::factory()->create(['email_address' => 'test@example.com']);
    Feedback::factory()->create(['email_address' => null]);

    $response = $this->withHeaders(['X-API-Key' => $this->apiKey])
        ->getJson('/api/feedback?has_email=true');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.email_address', 'test@example.com');
});

it('filters feedback by date range', function () {
    Feedback::factory()->create(['created_at' => now()->subDays(5)]);
    Feedback::factory()->create(['created_at' => now()->subDays(15)]);

    $response = $this->withHeaders(['X-API-Key' => $this->apiKey])
        ->getJson('/api/feedback?from_date=' . now()->subDays(10)->toDateString());

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data');
});

it('sorts feedback by specified field', function () {
    Feedback::factory()->create(['experiment' => 'B Experiment']);
    Feedback::factory()->create(['experiment' => 'A Experiment']);

    $response = $this->withHeaders(['X-API-Key' => $this->apiKey])
        ->getJson('/api/feedback?sort_by=experiment&sort_direction=asc');

    $response->assertStatus(200)
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.experiment', 'A Experiment');
});

it('filters feedback by PHP version', function () {
    Feedback::factory()->create(['php_version' => '8.1']);
    Feedback::factory()->create(['php_version' => '8.2']);

    $response = $this->withHeaders(['X-API-Key' => $this->apiKey])
        ->getJson('/api/feedback?php_version=8.1');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.php_version', '8.1');
});

it('filters feedback by Vanguard version', function () {
    Feedback::factory()->create(['vanguard_version' => '1.0.0']);
    Feedback::factory()->create(['vanguard_version' => '1.1.0']);

    $response = $this->withHeaders(['X-API-Key' => $this->apiKey])
        ->getJson('/api/feedback?vanguard_version=1.0.0');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.vanguard_version', '1.0.0');
});

it('filters feedback by email domain', function () {
    Feedback::factory()->create(['email_address' => 'test@example.com']);
    Feedback::factory()->create(['email_address' => 'test@other.com']);

    $response = $this->withHeaders(['X-API-Key' => $this->apiKey])
        ->getJson('/api/feedback?email_domain=example.com');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.email_domain', 'example.com');
});
