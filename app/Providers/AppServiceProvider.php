<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

/**
 * AppServiceProvider class for registering and bootstrapping application services.
 *
 * This service provider is responsible for registering application services
 * and performing any necessary bootstrapping operations.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * This method is called by the service container during the registration
     * phase of the bootstrap process. You may register any bindings or
     * singletons in this method.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * This method is called after all other service providers have been
     * registered. You're free to add your own initialization code here.
     */
    public function boot(): void
    {
        // Configure rate limiting for submissions
        RateLimiter::for('submissions', function (Request $request) {
            return Limit::perHour(10)->by($request->ip());
        });
    }
}
