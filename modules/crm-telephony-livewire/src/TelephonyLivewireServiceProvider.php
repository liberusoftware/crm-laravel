<?php

declare(strict_types=1);

namespace Liberu\CRM\Telephony\Livewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\Telephony\Livewire\Components\TelephonyDashboard;
use Livewire\Livewire;

final class TelephonyLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-crm-telephony::dashboard', TelephonyDashboard::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-telephony-livewire');
    }
}
