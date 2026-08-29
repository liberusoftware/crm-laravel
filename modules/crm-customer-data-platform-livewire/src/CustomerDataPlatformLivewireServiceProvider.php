<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerDataPlatformLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\CustomerDataPlatformLivewire\Livewire\CdpDashboard;
use Livewire\Livewire;

final class CustomerDataPlatformLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-crm-customer-data-platform-livewire');
        Livewire::component('module-crm-customer-data-platform-livewire::dashboard', CdpDashboard::class);
    }
}
