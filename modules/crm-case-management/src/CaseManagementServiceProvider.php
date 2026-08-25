<?php

declare(strict_types=1);

namespace Liberu\CRM\CaseManagement;

use Illuminate\Support\ServiceProvider;

final class CaseManagementServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
