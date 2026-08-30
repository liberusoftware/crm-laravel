<?php

declare(strict_types=1);

namespace Liberu\CRM\Forecasting;

use Illuminate\Support\ServiceProvider;

final class ForecastingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
