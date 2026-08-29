<?php

declare(strict_types=1);

namespace Liberu\CRM\AccountPlanning;

use Illuminate\Support\ServiceProvider;

final class AccountPlanningServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
