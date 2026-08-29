<?php

declare(strict_types=1);

namespace Liberu\CRM\RevenueLifecycle\Livewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\RevenueLifecycle\Livewire\Components\RevenueLifecycleDashboard;
use Livewire\Livewire;

final class RevenueLifecycleLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-crm-revenue-lifecycle::dashboard', RevenueLifecycleDashboard::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-revenue-lifecycle-livewire');
    }
}
