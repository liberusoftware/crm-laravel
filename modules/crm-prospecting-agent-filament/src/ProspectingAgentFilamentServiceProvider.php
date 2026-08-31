<?php

declare(strict_types=1);

namespace Liberu\CRM\ProspectingAgent\Filament;

use Illuminate\Support\ServiceProvider;

final class ProspectingAgentFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ProspectingAgentFilamentPlugin::class);
    }
}
