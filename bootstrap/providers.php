<?php

/**
 * Application Service Providers
 *
 * This array lists the service providers that will be loaded as part of the application bootstrap process.
 * Each provider is responsible for binding services in the container, registering events, or performing any other tasks.
 */

return [
    App\Providers\AppServiceProvider::class,
    Laravel\Sanctum\SanctumServiceProvider::class,
];
