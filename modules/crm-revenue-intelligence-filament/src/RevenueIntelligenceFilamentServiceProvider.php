<?php

declare(strict_types=1);

namespace Liberu\CRM\RevenueIntelligence\Filament;

use Illuminate\Support\ServiceProvider;

final class RevenueIntelligenceFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(RevenueIntelligenceFilamentPlugin::class);
    }
}
