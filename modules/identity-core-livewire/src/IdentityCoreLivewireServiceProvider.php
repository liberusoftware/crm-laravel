<?php

declare(strict_types=1);

namespace Liberu\Foundation\IdentityCoreLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class IdentityCoreLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'identity-core-livewire');
        Livewire::component('identity-core-livewire-overview', Liberu\Foundation\IdentityCoreLivewire\Livewire\Overview::class);
    }
}
