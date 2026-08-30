<?php

declare(strict_types=1);

namespace Liberu\CRM\WhiteLabel\Livewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\WhiteLabel\Livewire\Components\WhiteLabelSettings;
use Livewire\Livewire;

final class WhiteLabelLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-white-label-livewire');
        Livewire::component('module-crm-white-label::settings', WhiteLabelSettings::class);
    }
}
