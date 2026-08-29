<?php

declare(strict_types=1);

namespace Liberu\CRM\ReputationManagement\Livewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\ReputationManagement\Livewire\Components\ReputationDashboard;
use Livewire\Livewire;

final class ReputationManagementLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-crm-reputation-management::dashboard', ReputationDashboard::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-reputation-management-livewire');
    }
}
