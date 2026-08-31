<?php

declare(strict_types=1);

namespace Liberu\CRM\ServiceAnalytics\Filament;

use Illuminate\Support\ServiceProvider;

final class ServiceAnalyticsFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ServiceAnalyticsFilamentPlugin::class);
    }
}
