<?php

declare(strict_types=1);

namespace Liberu\CRM\CrmSearchLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\CrmSearchLivewire\Livewire\SearchDashboard;
use Livewire\Livewire;

final class CrmSearchLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-crm-search-livewire');
        Livewire::component('module-crm-search-livewire::dashboard', SearchDashboard::class);
    }
}
