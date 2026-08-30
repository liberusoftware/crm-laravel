<?php

declare(strict_types=1);

namespace Liberu\CRM\WorkManagement;

use Illuminate\Support\ServiceProvider;

final class WorkManagementServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Services\WorkloadQuery::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
