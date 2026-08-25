<?php

declare(strict_types=1);

namespace Liberu\CRM\Prospecting\Livewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\Prospecting\Livewire\Components\ProspectingDashboard;
use Livewire\Livewire;

final class ProspectingLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-crm-prospecting::dashboard', ProspectingDashboard::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-prospecting-livewire');
    }
}
