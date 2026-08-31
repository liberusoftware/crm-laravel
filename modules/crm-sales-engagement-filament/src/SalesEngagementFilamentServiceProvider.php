<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesEngagement\Filament;

use Illuminate\Support\ServiceProvider;

final class SalesEngagementFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SalesEngagementFilamentPlugin::class);
    }
}
