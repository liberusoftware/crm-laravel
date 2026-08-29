<?php

declare(strict_types=1);

namespace Liberu\CRM\RevenueLifecycle;

use Illuminate\Support\ServiceProvider;

final class RevenueLifecycleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
