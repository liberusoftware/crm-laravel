<?php

declare(strict_types=1);

namespace Liberu\CRM\SlaAndEntitlements;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\SlaAndEntitlements\Services\SlaAudit;

final class SlaAndEntitlementsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SlaAudit::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
