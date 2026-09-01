<?php

declare(strict_types=1);

namespace Liberu\CRM\CaseManagementFilament;

use Illuminate\Support\ServiceProvider;

final class CaseManagementFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CaseManagementFilamentPlugin::class);
    }
}
