<?php

declare(strict_types=1);

namespace Liberu\CRM\SandboxAndReleaseManagement\Filament;

use Illuminate\Support\ServiceProvider;

final class SandboxAndReleaseManagementFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SandboxAndReleaseManagementFilamentPlugin::class);
    }
}
