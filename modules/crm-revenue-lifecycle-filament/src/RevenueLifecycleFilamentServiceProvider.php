<?php

declare(strict_types=1);

namespace Liberu\CRM\RevenueLifecycle\Filament;

use Illuminate\Support\ServiceProvider;

final class RevenueLifecycleFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(RevenueLifecycleFilamentPlugin::class);
    }
}
