<?php

declare(strict_types=1);

namespace Liberu\CRM\SlaAndEntitlements\Livewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\SlaAndEntitlements\Livewire\Components\SlaDashboard;
use Livewire\Livewire;

final class SlaAndEntitlementsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-crm-sla-and-entitlements::dashboard', SlaDashboard::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-sla-and-entitlements-livewire');
    }
}
