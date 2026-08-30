<?php

declare(strict_types=1);

namespace Liberu\CRM\ConsentAndPreferences;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\ConsentAndPreferences\Services\PolicyEvaluator;

final class ConsentAndPreferencesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PolicyEvaluator::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
