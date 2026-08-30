<?php

declare(strict_types=1);

namespace Liberu\Foundation\JetstreamBridgeLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class JetstreamBridgeLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'jetstream-bridge-livewire');
        Livewire::component('jetstream-bridge-livewire-overview', Liberu\Foundation\JetstreamBridgeLivewire\Livewire\Overview::class);
    }
}
