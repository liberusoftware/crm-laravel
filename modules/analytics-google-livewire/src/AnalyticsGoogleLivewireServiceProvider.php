<?php

declare(strict_types=1);

namespace Liberu\Foundation\AnalyticsGoogleLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class AnalyticsGoogleLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'analytics-google-livewire');
        Livewire::component('analytics-google-livewire-overview', Liberu\Foundation\AnalyticsGoogleLivewire\Livewire\Overview::class);
    }
}
