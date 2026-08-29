<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerSuccessLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\CustomerSuccessLivewire\Livewire\SuccessDashboard;
use Livewire\Livewire;

final class CustomerSuccessLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-crm-customer-success-livewire');
        Livewire::component('module-crm-customer-success-livewire::dashboard', SuccessDashboard::class);
    }
}
