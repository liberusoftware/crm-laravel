<?php

declare(strict_types=1);

namespace Liberu\CRM\Routing\Livewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\Routing\Livewire\Components\RoutingDashboard;
use Livewire\Livewire;

final class RoutingLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-crm-routing::dashboard', RoutingDashboard::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-routing-livewire');
    }
}
