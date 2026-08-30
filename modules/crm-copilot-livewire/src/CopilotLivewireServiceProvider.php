<?php

declare(strict_types=1);

namespace Liberu\CRM\CopilotLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\CopilotLivewire\Livewire\CopilotDashboard;
use Livewire\Livewire;

final class CopilotLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-crm-copilot-livewire');
        Livewire::component('module-crm-copilot-livewire::dashboard', CopilotDashboard::class);
    }
}
