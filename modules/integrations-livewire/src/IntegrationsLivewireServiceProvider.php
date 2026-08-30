<?php

declare(strict_types=1);

namespace Liberu\Foundation\IntegrationsLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class IntegrationsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'integrations-livewire');
        Livewire::component('integrations-livewire-overview', Liberu\Foundation\IntegrationsLivewire\Livewire\Overview::class);
    }
}
