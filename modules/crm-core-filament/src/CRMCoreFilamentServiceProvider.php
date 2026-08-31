<?php

declare(strict_types=1);

namespace Liberu\CRM\Core\Filament;

use Illuminate\Support\ServiceProvider;

final class CRMCoreFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CRMCoreFilamentPlugin::class);
    }
}
