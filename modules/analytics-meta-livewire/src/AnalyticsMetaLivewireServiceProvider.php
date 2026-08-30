<?php

declare(strict_types=1);

namespace Liberu\Foundation\AnalyticsMetaLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class AnalyticsMetaLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'analytics-meta-livewire');
        Livewire::component('analytics-meta-livewire-overview', Liberu\Foundation\AnalyticsMetaLivewire\Livewire\Overview::class);
    }
}
