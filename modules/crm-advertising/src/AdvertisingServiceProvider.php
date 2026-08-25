<?php

declare(strict_types=1);

namespace Liberu\CRM\Advertising;

use Illuminate\Support\ServiceProvider;

final class AdvertisingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
