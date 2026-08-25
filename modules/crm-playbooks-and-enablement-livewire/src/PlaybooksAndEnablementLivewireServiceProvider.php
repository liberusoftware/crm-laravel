<?php

declare(strict_types=1);

namespace Liberu\CRM\PlaybooksAndEnablement\Livewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\PlaybooksAndEnablement\Livewire\Components\PlaybooksDashboard;
use Livewire\Livewire;

final class PlaybooksAndEnablementLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-crm-playbooks-and-enablement::dashboard', PlaybooksDashboard::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-playbooks-and-enablement-livewire');
    }
}
