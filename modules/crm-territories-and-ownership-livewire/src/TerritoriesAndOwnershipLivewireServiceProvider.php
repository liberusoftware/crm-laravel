<?php

declare(strict_types=1);

namespace Liberu\CRM\TerritoriesAndOwnership\Livewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\TerritoriesAndOwnership\Livewire\Components\TerritoryDashboard;
use Livewire\Livewire;

final class TerritoriesAndOwnershipLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-crm-territories-and-ownership::dashboard', TerritoryDashboard::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-territories-and-ownership-livewire');
    }
}
