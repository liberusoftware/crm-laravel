<?php

declare(strict_types=1);

namespace Liberu\CRM\SandboxAndReleaseManagement;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\SandboxAndReleaseManagement\Services\ReleaseAudit;

final class SandboxAndReleaseManagementServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ReleaseAudit::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
