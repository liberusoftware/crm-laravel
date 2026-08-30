<?php

declare(strict_types=1);

namespace Liberu\CRM\TerritoriesAndOwnership;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\TerritoriesAndOwnership\Services\TerritoryPolicy;

final class TerritoriesAndOwnershipServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TerritoryPolicy::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
