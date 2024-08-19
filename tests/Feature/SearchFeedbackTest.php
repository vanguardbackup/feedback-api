<?php

use App\Models\Feedback;
use Illuminate\Support\Facades\Config;

beforeEach(function () {
    Config::set('app.api_key', 'test-api-key');
    $this->apiKey = config('app.api_key');
});

it('searches feedback with a query', function () {
    Feedback::factory()->create([
        'experiment' => 'Test Experiment',
        'feedback' => 'This is a test feedback',
        'php_version' => '8.1',
        'vanguard_version' => '1.0.0',
        'email_address' => 'test@example.com',
    ]);

    Feedback::factory()->create([
        'experiment' => 'Another Experiment',
        'feedback' => 'This is another feedback',
        'php_version' => '8.2',
        'vanguard_version' => '1.1.0',
        'email_address' => 'another@example.com',
    ]);

    $response = $this->withHeaders(['X-API-Key' => $this->apiKey])
        ->getJson('/api/feedback/search?query=test');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.experiment', 'Test Experiment');
});

it('returns all feedback when no query is provided', function () {
    Feedback::factory()->count(2)->create();

    $response = $this->withHeaders(['X-API-Key' => $this->apiKey])
        ->getJson('/api/feedback/search');

    $response->assertStatus(200)
        ->assertJsonCount(2, 'data');
});

it('paginates search results', function () {
    Feedback::factory()->count(20)->create(['experiment' => 'Test Experiment']);

    $response = $this->withHeaders(['X-API-Key' => $this->apiKey])
        ->getJson('/api/feedback/search?query=Test&per_page=10');

    $response->assertStatus(200)
        ->assertJsonCount(10, 'data')
        ->assertJsonPath('meta.total', 20);
});

it('requires API key for search', function () {
    $response = $this->getJson('/api/feedback/search?query=test');

    $response->assertStatus(401);
});

it('filters feedback by email presence', function () {
    Feedback::factory()->create(['email_address' => 'test@example.com']);
    Feedback::factory()->create(['email_address' => null]);

    $response = $this->withHeaders(['X-API-Key' => $this->apiKey])
        ->getJson('/api/feedback/search?has_email=true');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.email_address', 'test@example.com');
});

it('filters feedback by date range', function () {
    Feedback::factory()->create(['created_at' => now()->subDays(5)]);
    Feedback::factory()->create(['created_at' => now()->subDays(15)]);

    $response = $this->withHeaders(['X-API-Key' => $this->apiKey])
        ->getJson('/api/feedback/search?from_date=' . now()->subDays(10)->toDateString());

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data');
});

it('sorts feedback by specified field', function () {
    Feedback::factory()->create(['experiment' => 'B Experiment']);
    Feedback::factory()->create(['experiment' => 'A Experiment']);

    $response = $this->withHeaders(['X-API-Key' => $this->apiKey])
        ->getJson('/api/feedback/search?sort_by=experiment&sort_direction=asc');

    $response->assertStatus(200)
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.experiment', 'A Experiment');
});

it('filters feedback by specific experiment', function () {
    Feedback::factory()->create(['experiment' => 'Test Experiment']);
    Feedback::factory()->create(['experiment' => 'Another Experiment']);

    $response = $this->withHeaders(['X-API-Key' => $this->apiKey])
        ->getJson('/api/feedback/search?experiment=Test Experiment');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.experiment', 'Test Experiment');
});

it('filters feedback by PHP version', function () {
    Feedback::factory()->create(['php_version' => '8.1']);
    Feedback::factory()->create(['php_version' => '8.2']);

    $response = $this->withHeaders(['X-API-Key' => $this->apiKey])
        ->getJson('/api/feedback/search?php_version=8.1');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.php_version', '8.1');
});

it('filters feedback by Vanguard version', function () {
    Feedback::factory()->create(['vanguard_version' => '1.0.0']);
    Feedback::factory()->create(['vanguard_version' => '1.1.0']);

    $response = $this->withHeaders(['X-API-Key' => $this->apiKey])
        ->getJson('/api/feedback/search?vanguard_version=1.0.0');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.vanguard_version', '1.0.0');
});
