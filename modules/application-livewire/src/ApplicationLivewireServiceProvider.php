<?php

declare(strict_types=1);

namespace Liberu\Foundation\ApplicationLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class ApplicationLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'application-livewire');
        Livewire::component('application-livewire-overview', Liberu\Foundation\ApplicationLivewire\Livewire\Overview::class);
    }
}
