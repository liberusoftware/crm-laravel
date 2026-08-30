<?php

declare(strict_types=1);

namespace Liberu\CRM\ProposalsAndQuotes\Livewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\ProposalsAndQuotes\Livewire\Components\ProposalDashboard;
use Livewire\Livewire;

final class ProposalsAndQuotesLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-crm-proposals-and-quotes::dashboard', ProposalDashboard::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-proposals-and-quotes-livewire');
    }
}
