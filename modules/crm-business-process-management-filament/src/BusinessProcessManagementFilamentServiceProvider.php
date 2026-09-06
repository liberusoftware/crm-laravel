<?php

declare(strict_types=1);

namespace Liberu\CRM\BusinessProcessManagementFilament;

use Illuminate\Support\ServiceProvider;

final class BusinessProcessManagementFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(BusinessProcessManagementFilamentPlugin::class);
    }
}
