<?php

declare(strict_types=1);

namespace Liberu\CRM\Scheduling\Filament;

use Illuminate\Support\ServiceProvider;

final class SchedulingFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SchedulingFilamentPlugin::class);
    }
}
