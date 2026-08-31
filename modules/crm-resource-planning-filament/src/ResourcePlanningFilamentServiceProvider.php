<?php

declare(strict_types=1);

namespace Liberu\CRM\ResourcePlanning\Filament;

use Illuminate\Support\ServiceProvider;

final class ResourcePlanningFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ResourcePlanningFilamentPlugin::class);
    }
}
