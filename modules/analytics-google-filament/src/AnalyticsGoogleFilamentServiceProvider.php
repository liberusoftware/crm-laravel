<?php

declare(strict_types=1);

namespace Liberu\Foundation\AnalyticsGoogleFilament;

use Illuminate\Support\ServiceProvider;

final class AnalyticsGoogleFilamentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'analytics-google-filament');
    }
}
