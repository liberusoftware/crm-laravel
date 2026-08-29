<?php

declare(strict_types=1);

namespace Liberu\CRM\ConversionOptimization;

use Illuminate\Support\ServiceProvider;

final class ConversionOptimizationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
