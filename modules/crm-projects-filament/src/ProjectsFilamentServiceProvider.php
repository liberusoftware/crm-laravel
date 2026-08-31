<?php

declare(strict_types=1);

namespace Liberu\CRM\Projects\Filament;

use Illuminate\Support\ServiceProvider;

final class ProjectsFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ProjectsFilamentPlugin::class);
    }
}
