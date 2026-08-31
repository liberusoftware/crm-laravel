<?php

declare(strict_types=1);

namespace Liberu\CRM\WorkManagement\Filament;

use Illuminate\Support\ServiceProvider;

final class WorkManagementFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(WorkManagementFilamentPlugin::class);
    }
}
