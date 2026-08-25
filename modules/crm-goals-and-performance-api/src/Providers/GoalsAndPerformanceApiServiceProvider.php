<?php

declare(strict_types=1);

namespace Liberu\CRM\GoalsAndPerformanceApi\Providers;

use Illuminate\Support\ServiceProvider;

final class GoalsAndPerformanceApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
