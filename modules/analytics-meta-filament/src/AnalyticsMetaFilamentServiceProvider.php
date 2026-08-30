<?php

declare(strict_types=1);

namespace Liberu\Foundation\AnalyticsMetaFilament;

use Illuminate\Support\ServiceProvider;

final class AnalyticsMetaFilamentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'analytics-meta-filament');
    }
}
