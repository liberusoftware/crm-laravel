<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesEngagement\Livewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\SalesEngagement\Livewire\Components\EngagementDashboard;
use Livewire\Livewire;

final class SalesEngagementLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('crm-sales-engagement-dashboard', EngagementDashboard::class);
        Livewire::resolveMissingComponent(fn (string $name): ?string => $name === 'module-crm-sales-engagement::dashboard' ? EngagementDashboard::class : null);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-sales-engagement-livewire');
    }
}
