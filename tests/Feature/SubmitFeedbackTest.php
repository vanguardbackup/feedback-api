<?php

use Illuminate\Support\Facades\RateLimiter;

beforeEach(function () {
    RateLimiter::clear('feedback');
});

it('can submit feedback without email', function () {
    $response = $this->postJson('/api/feedback', [
        'experiment' => 'Test Experiment',
        'feedback' => 'This is a test feedback',
        'php_version' => '8.1',
        'vanguard_version' => '1.0.0',
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'message' => 'Experiment feedback successfully submitted',
            'status' => 'success',
        ]);

    $this->assertDatabaseHas('feedback', [
        'experiment' => 'Test Experiment',
        'feedback' => 'This is a test feedback',
        'php_version' => '8.1',
        'vanguard_version' => '1.0.0',
        'email_address' => null,
    ]);
});

it('can submit feedback with email', function () {
    $response = $this->postJson('/api/feedback', [
        'experiment' => 'Test Experiment',
        'feedback' => 'This is a test feedback',
        'php_version' => '8.1',
        'vanguard_version' => '1.0.0',
        'email_address' => 'test@example.com',
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'message' => 'Experiment feedback successfully submitted',
            'status' => 'success',
        ]);

    $this->assertDatabaseHas('feedback', [
        'experiment' => 'Test Experiment',
        'feedback' => 'This is a test feedback',
        'php_version' => '8.1',
        'vanguard_version' => '1.0.0',
        'email_address' => 'test@example.com',
    ]);
});

it('filters any profanity in the submission', function () {
    $response = $this->postJson('/api/feedback', [
        'experiment' => 'Test Experiment',
        'feedback' => 'This feedback is fucking terrible.',
        'php_version' => '8.1',
        'vanguard_version' => '1.0.0',
        'email_address' => 'test@example.com',
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'message' => 'Experiment feedback successfully submitted',
            'status' => 'success',
        ]);

    $this->assertDatabaseHas('feedback', [
        'experiment' => 'Test Experiment',
        'feedback' => 'This feedback is ******* terrible.',
        'php_version' => '8.1',
        'vanguard_version' => '1.0.0',
        'email_address' => 'test@example.com',
    ]);
});

it('validates required fields', function () {
    $response = $this->postJson('/api/feedback', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['experiment', 'feedback', 'php_version', 'vanguard_version']);
});

it('validates email format', function () {
    $response = $this->postJson('/api/feedback', [
        'experiment' => 'Test Experiment',
        'feedback' => 'This is a test feedback',
        'php_version' => '8.1',
        'vanguard_version' => '1.0.0',
        'email_address' => 'invalid-email',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email_address']);
});

it('respects rate limiting', function () {
    // Make 5 requests (the limit we set)
    for ($i = 0; $i < 5; $i++) {
        $this->postJson('/api/feedback', [
            'experiment' => 'Rate Limit Test',
            'feedback' => 'Test feedback ' . $i,
            'php_version' => '8.1',
            'vanguard_version' => '1.0.0',
        ])->assertStatus(201);
    }

    // The 6th request should be rate limited
    $response = $this->postJson('/api/feedback', [
        'experiment' => 'Rate Limit Test',
        'feedback' => 'This should be rate limited',
        'php_version' => '8.1',
        'vanguard_version' => '1.0.0',
    ]);

    $response->assertStatus(429); // Too Many Requests
});

it('allows requests after rate limit window', function () {
    // Make 5 requests
    for ($i = 0; $i < 5; $i++) {
        $this->postJson('/api/feedback', [
            'experiment' => 'Rate Limit Test',
            'feedback' => 'Test feedback ' . $i,
            'php_version' => '8.1',
            'vanguard_version' => '1.0.0',
        ])->assertStatus(201);
    }

    // Simulate waiting for 1 minute
    $this->travel(1)->minute();

    // This request should now be allowed
    $response = $this->postJson('/api/feedback', [
        'experiment' => 'Rate Limit Test',
        'feedback' => 'This should be allowed',
        'php_version' => '8.1',
        'vanguard_version' => '1.0.0',
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'message' => 'Experiment feedback successfully submitted',
            'status' => 'success',
        ]);
});

it('trims whitespace from input fields', function () {
    $response = $this->postJson('/api/feedback', [
        'experiment' => '  Trimmed Experiment  ',
        'feedback' => '  This feedback should be trimmed  ',
        'php_version' => '  8.1  ',
        'vanguard_version' => '  1.0.0  ',
        'email_address' => '  test@example.com  ',
    ]);

    $response->assertStatus(201);

    $this->assertDatabaseHas('feedback', [
        'experiment' => 'Trimmed Experiment',
        'feedback' => 'This feedback should be trimmed',
        'php_version' => '8.1',
        'vanguard_version' => '1.0.0',
        'email_address' => 'test@example.com',
    ]);
});
