<?php

declare(strict_types=1);

namespace Liberu\CRM\ResourcePlanning\Livewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\ResourcePlanning\Livewire\Components\ResourcePlanningDashboard;
use Livewire\Livewire;

final class ResourcePlanningLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-crm-resource-planning::dashboard', ResourcePlanningDashboard::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-resource-planning-livewire');
    }
}
