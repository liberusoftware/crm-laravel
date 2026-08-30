<?php

declare(strict_types=1);

namespace Liberu\Foundation\AnalyticsCoreLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class AnalyticsCoreLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'analytics-core-livewire');
        Livewire::component('analytics-core-livewire-overview', Liberu\Foundation\AnalyticsCoreLivewire\Livewire\Overview::class);
    }
}
