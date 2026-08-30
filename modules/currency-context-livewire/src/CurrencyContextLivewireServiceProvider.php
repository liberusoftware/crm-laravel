<?php

declare(strict_types=1);

namespace Liberu\Foundation\CurrencyContextLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class CurrencyContextLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'currency-context-livewire');
        Livewire::component('currency-context-livewire-overview', Liberu\Foundation\CurrencyContextLivewire\Livewire\Overview::class);
    }
}
