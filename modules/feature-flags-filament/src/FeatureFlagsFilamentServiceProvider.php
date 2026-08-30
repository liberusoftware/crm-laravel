<?php

declare(strict_types=1);

namespace Liberu\Foundation\FeatureFlagsFilament;

use Illuminate\Support\ServiceProvider;

final class FeatureFlagsFilamentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'feature-flags-filament');
    }
}
