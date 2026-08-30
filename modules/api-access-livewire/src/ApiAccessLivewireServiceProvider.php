<?php

declare(strict_types=1);

namespace Liberu\Foundation\ApiAccessLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class ApiAccessLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'api-access-livewire');
        Livewire::component('api-access-livewire-overview', Liberu\Foundation\ApiAccessLivewire\Livewire\Overview::class);
    }
}
