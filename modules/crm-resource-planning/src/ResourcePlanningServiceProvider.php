<?php

declare(strict_types=1);

namespace Liberu\CRM\ResourcePlanning;

use Illuminate\Support\ServiceProvider;

final class ResourcePlanningServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
