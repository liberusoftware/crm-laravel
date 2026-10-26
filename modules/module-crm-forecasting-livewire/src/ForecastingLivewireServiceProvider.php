<?php

declare(strict_types=1);

namespace Liberu\CRM\ForecastingLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\ForecastingLivewire\Livewire\ForecastDashboard;
use Livewire\Livewire;

final class ForecastingLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-crm-forecasting-livewire');
        Livewire::component('module-crm-forecasting-livewire::dashboard', ForecastDashboard::class);
    }
}
