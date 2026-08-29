<?php

declare(strict_types=1);

namespace Liberu\CRM\GoalsAndPerformance;

use Illuminate\Support\ServiceProvider;

final class GoalsAndPerformanceServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
