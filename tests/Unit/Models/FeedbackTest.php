<?php

use App\Models\Feedback;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Feedback Model', function () {
    beforeEach(function () {
        // Set up some test data
        Feedback::factory()->create([
            'experiment' => 'ExperimentA',
            'php_version' => '7.4',
            'vanguard_version' => '1.0.0',
            'feedback' => 'This is a test feedback for ExperimentA',
            'email_address' => 'userA@example.com',
            'created_at' => now()->subDays(5),
        ]);

        Feedback::factory()->create([
            'experiment' => 'ExperimentB',
            'php_version' => '8.0',
            'vanguard_version' => '2.0.0',
            'feedback' => 'This is a test feedback for ExperimentB',
            'email_address' => null,
            'created_at' => now()->subDays(10),
        ]);
    });

    it('can filter feedback by experiment', function () {
        $feedbackA = Feedback::forExperiment('ExperimentA')->get();
        $feedbackB = Feedback::forExperiment('ExperimentB')->get();

        expect($feedbackA)->toHaveCount(1)
            ->and($feedbackB)->toHaveCount(1)
            ->and($feedbackA->first()->experiment)->toBe('ExperimentA')
            ->and($feedbackB->first()->experiment)->toBe('ExperimentB');
    });

    it('can filter feedback by PHP version', function () {
        $feedback74 = Feedback::forPhpVersion('7.4')->get();
        $feedback80 = Feedback::forPhpVersion('8.0')->get();

        expect($feedback74)->toHaveCount(1)
            ->and($feedback80)->toHaveCount(1)
            ->and($feedback74->first()->php_version)->toBe('7.4')
            ->and($feedback80->first()->php_version)->toBe('8.0');
    });

    it('can filter feedback by Vanguard version', function () {
        $feedback100 = Feedback::forVanguardVersion('1.0.0')->get();
        $feedback200 = Feedback::forVanguardVersion('2.0.0')->get();

        expect($feedback100)->toHaveCount(1)
            ->and($feedback200)->toHaveCount(1)
            ->and($feedback100->first()->vanguard_version)->toBe('1.0.0')
            ->and($feedback200->first()->vanguard_version)->toBe('2.0.0');
    });

    it('can filter recent feedback', function () {
        $recentFeedback = Feedback::recent(7)->get();

        expect($recentFeedback)->toHaveCount(1)
            ->and($recentFeedback->first()->experiment)->toBe('ExperimentA');
    });

    it('can get a summary of the feedback', function () {
        $feedback = Feedback::first();
        $summary = $feedback->getSummary(20);

        expect($summary)->toBe('This is a test feedb...');
    });

    it('can check if feedback is for a specific experiment', function () {
        $feedback = Feedback::first();

        expect($feedback->isForExperiment('ExperimentA'))->toBeTrue()
            ->and($feedback->isForExperiment('ExperimentB'))->toBeFalse();
    });

    it('can filter feedback with email', function () {
        $withEmail = Feedback::withEmail()->get();
        $withoutEmail = Feedback::withoutEmail()->get();

        expect($withEmail)->toHaveCount(1)
            ->and($withoutEmail)->toHaveCount(1)
            ->and($withEmail->first()->email_address)->not->toBeNull()
            ->and($withoutEmail->first()->email_address)->toBeNull();
    });

    it('can check if feedback has an email address', function () {
        $feedbackA = Feedback::where('experiment', 'ExperimentA')->first();
        $feedbackB = Feedback::where('experiment', 'ExperimentB')->first();

        expect($feedbackA->hasEmailAddress())->toBeTrue()
            ->and($feedbackB->hasEmailAddress())->toBeFalse();
    });

    it('can get email domain', function () {
        $feedback = Feedback::where('experiment', 'ExperimentA')->first();

        expect($feedback->getEmailDomain())->toBe('example.com');
    });

    it('can filter feedback by email domain', function () {
        $feedback = Feedback::fromEmailDomain('example.com')->get();

        expect($feedback)->toHaveCount(1)
            ->and($feedback->first()->email_address)->toBe('userA@example.com');
    });

    it('can get feedback age in days', function () {
        $feedbackA = Feedback::where('experiment', 'ExperimentA')->first();
        $feedbackB = Feedback::where('experiment', 'ExperimentB')->first();

        expect($feedbackA->getAgeInDays())->toBe(5)
            ->and($feedbackB->getAgeInDays())->toBe(10);
    });

    it('can check if feedback is older than a given number of days', function () {
        $feedbackA = Feedback::where('experiment', 'ExperimentA')->first();
        $feedbackB = Feedback::where('experiment', 'ExperimentB')->first();

        expect($feedbackA->isOlderThan(4))->toBeTrue()
            ->and($feedbackA->isOlderThan(6))->toBeFalse()
            ->and($feedbackB->isOlderThan(9))->toBeTrue()
            ->and($feedbackB->isOlderThan(11))->toBeFalse();
    });
});
