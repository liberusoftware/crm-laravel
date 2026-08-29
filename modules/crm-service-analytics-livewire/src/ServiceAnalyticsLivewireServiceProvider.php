<?php

declare(strict_types=1);

namespace Liberu\CRM\ServiceAnalytics\Livewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\ServiceAnalytics\Livewire\Components\AnalyticsDashboard;
use Livewire\Livewire;

final class ServiceAnalyticsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-crm-service-analytics::dashboard', AnalyticsDashboard::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-service-analytics-livewire');
    }
}
