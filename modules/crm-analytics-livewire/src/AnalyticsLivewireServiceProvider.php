<?php

declare(strict_types=1);

namespace Liberu\CRM\AnalyticsLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\AnalyticsLivewire\Livewire\AnalyticsDashboard;
use Livewire\Livewire;

final class AnalyticsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-crm-analytics-livewire::dashboard', AnalyticsDashboard::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-crm-analytics-livewire');
    }
}
