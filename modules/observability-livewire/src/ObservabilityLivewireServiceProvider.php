<?php

declare(strict_types=1);

namespace Liberu\Foundation\ObservabilityLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class ObservabilityLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'observability-livewire');
        Livewire::component('observability-livewire-overview', Liberu\Foundation\ObservabilityLivewire\Livewire\Overview::class);
    }
}
