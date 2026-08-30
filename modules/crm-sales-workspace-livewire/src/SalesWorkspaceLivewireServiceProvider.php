<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesWorkspace\Livewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\SalesWorkspace\Livewire\Components\WorkspaceDashboard;
use Livewire\Livewire;

final class SalesWorkspaceLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-crm-sales-workspace::dashboard', WorkspaceDashboard::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-sales-workspace-livewire');
    }
}
