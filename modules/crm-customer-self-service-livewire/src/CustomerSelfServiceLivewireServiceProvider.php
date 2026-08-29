<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerSelfServiceLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\CustomerSelfServiceLivewire\Livewire\SelfServiceDashboard;
use Livewire\Livewire;

final class CustomerSelfServiceLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-crm-customer-self-service-livewire');
        Livewire::component('module-crm-customer-self-service-livewire::dashboard', SelfServiceDashboard::class);
    }
}
