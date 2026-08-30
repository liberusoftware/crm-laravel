<?php

declare(strict_types=1);

namespace Liberu\Foundation\AnalyticsCoreFilament;

use Illuminate\Support\ServiceProvider;

final class AnalyticsCoreFilamentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'analytics-core-filament');
    }
}
