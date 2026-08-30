<?php

declare(strict_types=1);

namespace Liberu\CRM\SlaAndEntitlements\Filament;

use Illuminate\Support\ServiceProvider;

final class SlaAndEntitlementsFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SlaAndEntitlementsFilamentPlugin::class);
    }
}
