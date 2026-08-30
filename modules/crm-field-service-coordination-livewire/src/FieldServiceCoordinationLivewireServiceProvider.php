<?php

declare(strict_types=1);

namespace Liberu\CRM\FieldServiceCoordinationLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\FieldServiceCoordinationLivewire\Livewire\FieldServiceDashboard;
use Livewire\Livewire;

final class FieldServiceCoordinationLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-crm-field-service-coordination-livewire');
        Livewire::component('module-crm-field-service-coordination-livewire::dashboard', FieldServiceDashboard::class);
    }
}
