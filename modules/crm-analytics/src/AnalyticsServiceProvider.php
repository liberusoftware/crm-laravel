<?php

declare(strict_types=1);

namespace Liberu\CRM\Analytics;

use Illuminate\Support\ServiceProvider;

final class AnalyticsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
