<?php

declare(strict_types=1);

namespace Liberu\CRM\Routing\Filament;

use Illuminate\Support\ServiceProvider;

final class RoutingFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(RoutingFilamentPlugin::class);
    }
}
