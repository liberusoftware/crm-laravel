<?php

declare(strict_types=1);

namespace Liberu\CRM\ClientOnboarding;

use Illuminate\Support\ServiceProvider;

final class ClientOnboardingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
