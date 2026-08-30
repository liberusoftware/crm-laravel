<?php

declare(strict_types=1);

namespace Liberu\Foundation\ModuleManagerLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class ModuleManagerLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-manager-livewire');
        Livewire::component('module-manager-livewire-overview', Liberu\Foundation\ModuleManagerLivewire\Livewire\Overview::class);
    }
}
