<?php

declare(strict_types=1);

namespace Liberu\CRM\ServiceAnalytics;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\ServiceAnalytics\Services\AnalyticsAudit;

final class ServiceAnalyticsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AnalyticsAudit::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
