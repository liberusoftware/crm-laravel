<?php

declare(strict_types=1);

namespace Liberu\CRM\QuotasAndIncentives\Livewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\QuotasAndIncentives\Livewire\Components\QuotaDashboard;
use Livewire\Livewire;

final class QuotasAndIncentivesLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-crm-quotas-and-incentives::dashboard', QuotaDashboard::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-quotas-and-incentives-livewire');
    }
}
