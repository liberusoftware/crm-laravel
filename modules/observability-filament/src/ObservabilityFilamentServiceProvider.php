<?php

declare(strict_types=1);

namespace Liberu\Foundation\ObservabilityFilament;

use Illuminate\Support\ServiceProvider;

final class ObservabilityFilamentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'observability-filament');
    }
}
