<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerDataPlatform;

use Illuminate\Support\ServiceProvider;

final class CustomerDataPlatformServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
