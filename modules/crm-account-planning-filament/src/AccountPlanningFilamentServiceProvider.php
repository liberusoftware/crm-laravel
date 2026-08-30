<?php

declare(strict_types=1);

namespace Liberu\CRM\AccountPlanningFilament;

use Illuminate\Support\ServiceProvider;

final class AccountPlanningFilamentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-account-planning-filament');
    }
}
