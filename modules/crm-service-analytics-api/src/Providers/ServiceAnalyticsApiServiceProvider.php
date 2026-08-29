<?php

declare(strict_types=1);

namespace Liberu\CRM\ServiceAnalyticsApi\Providers;

use Illuminate\Support\ServiceProvider;

final class ServiceAnalyticsApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
    }
}
