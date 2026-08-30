<?php

declare(strict_types=1);

namespace Liberu\Foundation\SettingsLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class SettingsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'settings-livewire');
        Livewire::component('settings-livewire-overview', Liberu\Foundation\SettingsLivewire\Livewire\Overview::class);
    }
}
