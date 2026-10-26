<?php

declare(strict_types=1);

namespace Liberu\CRM\ContractsLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\ContractsLivewire\Components\ContractBrowser;
use Livewire\Livewire;

final class ContractsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('crm-contracts::contract-browser', ContractBrowser::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-contracts');
    }
}
