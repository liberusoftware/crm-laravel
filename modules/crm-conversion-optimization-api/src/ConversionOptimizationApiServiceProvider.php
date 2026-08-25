<?php

declare(strict_types=1);

namespace Liberu\CRM\ConversionOptimizationApi;

use Illuminate\Support\ServiceProvider;

final class ConversionOptimizationApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
