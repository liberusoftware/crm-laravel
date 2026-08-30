<?php

declare(strict_types=1);

namespace Liberu\Foundation\JetstreamBridgeFilament;

use Illuminate\Support\ServiceProvider;

final class JetstreamBridgeFilamentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'jetstream-bridge-filament');
    }
}
