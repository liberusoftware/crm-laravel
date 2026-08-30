<?php

declare(strict_types=1);

namespace Liberu\CRM\EnrichmentLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\EnrichmentLivewire\Livewire\EnrichmentDashboard;
use Livewire\Livewire;

final class EnrichmentLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-crm-enrichment-livewire');
        Livewire::component('module-crm-enrichment-livewire::dashboard', EnrichmentDashboard::class);
    }
}
