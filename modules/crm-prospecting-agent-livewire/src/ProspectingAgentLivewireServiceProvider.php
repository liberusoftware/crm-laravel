<?php

declare(strict_types=1);

namespace Liberu\CRM\ProspectingAgent\Livewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\ProspectingAgent\Livewire\Components\ProspectingAgentDashboard;
use Livewire\Livewire;

final class ProspectingAgentLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-crm-prospecting-agent::dashboard', ProspectingAgentDashboard::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-prospecting-agent-livewire');
    }
}
