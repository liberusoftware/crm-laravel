<?php

declare(strict_types=1);

namespace Liberu\CRM\PredictiveModels\Livewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\PredictiveModels\Livewire\Components\PredictiveModelsDashboard;
use Livewire\Livewire;

final class PredictiveModelsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-crm-predictive-models::dashboard', PredictiveModelsDashboard::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-predictive-models-livewire');
    }
}
