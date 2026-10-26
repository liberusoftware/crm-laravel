<?php

declare(strict_types=1);

namespace Liberu\CRM\AnalyticsApi;

use Illuminate\Support\ServiceProvider;

final class AnalyticsApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
