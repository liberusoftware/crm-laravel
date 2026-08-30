<?php

declare(strict_types=1);

namespace Liberu\Foundation\IntegrationsFilament;

use Illuminate\Support\ServiceProvider;

final class IntegrationsFilamentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'integrations-filament');
    }
}
