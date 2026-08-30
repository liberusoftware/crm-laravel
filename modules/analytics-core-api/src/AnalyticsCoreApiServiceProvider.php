<?php

declare(strict_types=1);

namespace Liberu\Foundation\AnalyticsCoreApi;

use Illuminate\Support\ServiceProvider;

final class AnalyticsCoreApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
