<?php

declare(strict_types=1);

namespace Liberu\CRM\AdvertisingLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\AdvertisingLivewire\Components\AdvertisingWorkspace;
use Livewire\Livewire;

final class AdvertisingLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-advertising-livewire');
        Livewire::component('module-crm-advertising::workspace', AdvertisingWorkspace::class);
    }
}
