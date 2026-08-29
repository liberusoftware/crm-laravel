<?php

declare(strict_types=1);

namespace Liberu\CRM\AutomationPackLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\AutomationPackLivewire\Livewire\AutomationDashboard;
use Livewire\Livewire;

final class AutomationPackLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-crm-automation-pack-livewire');
        Livewire::component('module-crm-automation-pack-livewire::dashboard', AutomationDashboard::class);
    }
}
