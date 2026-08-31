<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesWorkspace\Filament;

use Illuminate\Support\ServiceProvider;

final class SalesWorkspaceFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SalesWorkspaceFilamentPlugin::class);
    }
}
