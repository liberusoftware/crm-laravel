<?php

declare(strict_types=1);

namespace Liberu\CRM\SandboxAndReleaseManagement\Livewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\SandboxAndReleaseManagement\Livewire\Components\ReleaseDashboard;
use Livewire\Livewire;

final class SandboxAndReleaseManagementLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-crm-sandbox-and-release-management::dashboard', ReleaseDashboard::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-sandbox-and-release-management-livewire');
    }
}
