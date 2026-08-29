<?php

declare(strict_types=1);

namespace Liberu\CRM\Scheduling\Livewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\Scheduling\Livewire\Components\SchedulingDashboard;
use Livewire\Livewire;

final class SchedulingLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-crm-scheduling::dashboard', SchedulingDashboard::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-scheduling-livewire');
    }
}
