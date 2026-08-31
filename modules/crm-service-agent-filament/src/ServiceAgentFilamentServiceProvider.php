<?php

declare(strict_types=1);

namespace Liberu\CRM\ServiceAgent\Filament;

use Illuminate\Support\ServiceProvider;

final class ServiceAgentFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ServiceAgentFilamentPlugin::class);
    }
}
