<?php

declare(strict_types=1);

namespace Liberu\CRM\DialerAndOutreachLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\DialerAndOutreachLivewire\Livewire\DialerDashboard;
use Livewire\Livewire;

final class DialerAndOutreachLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-crm-dialer-and-outreach-livewire');
        Livewire::component('module-crm-dialer-and-outreach-livewire::dashboard', DialerDashboard::class);
    }
}
