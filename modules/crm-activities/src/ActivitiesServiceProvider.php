<?php

declare(strict_types=1);

namespace Liberu\CRM\Activities;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\Activities\Services\ActivityScheduler;

final class ActivitiesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ActivityScheduler::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
