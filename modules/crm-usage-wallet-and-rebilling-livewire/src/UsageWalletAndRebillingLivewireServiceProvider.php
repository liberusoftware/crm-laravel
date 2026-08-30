<?php

declare(strict_types=1);

namespace Liberu\CRM\UsageWalletAndRebilling\Livewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\UsageWalletAndRebilling\Livewire\Components\UsageWalletDashboard;
use Livewire\Livewire;

final class UsageWalletAndRebillingLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-crm-usage-wallet-and-rebilling::dashboard', UsageWalletDashboard::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-usage-wallet-and-rebilling-livewire');
    }
}
