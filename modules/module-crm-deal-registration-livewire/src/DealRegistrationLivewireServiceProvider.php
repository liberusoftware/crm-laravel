<?php

declare(strict_types=1);

namespace Liberu\CRM\DealRegistrationLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\DealRegistrationLivewire\Livewire\DealDashboard;
use Livewire\Livewire;

final class DealRegistrationLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-crm-deal-registration-livewire');
        Livewire::component('module-crm-deal-registration-livewire::dashboard', DealDashboard::class);
    }
}
