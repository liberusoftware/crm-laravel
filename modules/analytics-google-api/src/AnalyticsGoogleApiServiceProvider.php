<?php

declare(strict_types=1);

namespace Liberu\Foundation\AnalyticsGoogleApi;

use Illuminate\Support\ServiceProvider;

final class AnalyticsGoogleApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
