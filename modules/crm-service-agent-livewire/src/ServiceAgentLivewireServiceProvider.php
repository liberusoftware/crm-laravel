<?php

declare(strict_types=1);

namespace Liberu\CRM\ServiceAgent\Livewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\ServiceAgent\Livewire\Components\AgentDashboard;
use Livewire\Livewire;

final class ServiceAgentLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-crm-service-agent::dashboard', AgentDashboard::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-service-agent-livewire');
    }
}
