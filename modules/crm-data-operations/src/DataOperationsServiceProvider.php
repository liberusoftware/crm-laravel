<?php

declare(strict_types=1);

namespace Liberu\CRM\DataOperations;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\DataOperations\Services\DataOperationService;

final class DataOperationsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(DataOperationService::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
