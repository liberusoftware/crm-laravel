<?php

declare(strict_types=1);

namespace Liberu\CRM\ReputationManagement\Filament;

use Illuminate\Support\ServiceProvider;

final class ReputationManagementFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ReputationManagementFilamentPlugin::class);
    }
}
