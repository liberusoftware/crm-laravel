<?php

declare(strict_types=1);

namespace Liberu\CRM\BusinessProcessManagement;

use Illuminate\Support\ServiceProvider;

final class BusinessProcessManagementServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
