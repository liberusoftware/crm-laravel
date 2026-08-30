<?php

declare(strict_types=1);

namespace Liberu\CRM\UsageWalletAndRebilling;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\UsageWalletAndRebilling\Services\UsageAudit;
use Liberu\CRM\UsageWalletAndRebilling\Services\UsagePolicy;

final class UsageWalletAndRebillingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(UsagePolicy::class);
        $this->app->singleton(UsageAudit::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
