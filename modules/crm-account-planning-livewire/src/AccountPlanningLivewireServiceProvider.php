<?php

declare(strict_types=1);

namespace Liberu\CRM\AccountPlanningLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\AccountPlanningLivewire\Components\AccountPlanningWorkspace;
use Livewire\Livewire;

final class AccountPlanningLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-account-planning-livewire');
        Livewire::component('module-crm-account-planning::workspace', AccountPlanningWorkspace::class);
    }
}
