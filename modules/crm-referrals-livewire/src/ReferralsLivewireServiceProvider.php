<?php

declare(strict_types=1);

namespace Liberu\CRM\Referrals\Livewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\Referrals\Livewire\Components\ReferralsDashboard;
use Livewire\Livewire;

final class ReferralsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-crm-referrals::dashboard', ReferralsDashboard::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-referrals-livewire');
    }
}
