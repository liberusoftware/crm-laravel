<?php

declare(strict_types=1);

namespace Liberu\CRM\RevenueIntelligence\Livewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\RevenueIntelligence\Livewire\Components\RevenueIntelligenceDashboard;
use Livewire\Livewire;

final class RevenueIntelligenceLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-crm-revenue-intelligence::dashboard', RevenueIntelligenceDashboard::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-revenue-intelligence-livewire');
    }
}
