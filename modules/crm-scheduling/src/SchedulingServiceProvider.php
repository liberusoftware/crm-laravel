<?php

declare(strict_types=1);

namespace Liberu\CRM\Scheduling;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\Scheduling\Services\SchedulingAudit;

final class SchedulingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SchedulingAudit::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
