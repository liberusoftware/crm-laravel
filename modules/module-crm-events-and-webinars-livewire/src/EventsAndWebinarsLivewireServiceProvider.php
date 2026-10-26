<?php

declare(strict_types=1);

namespace Liberu\CRM\EventsAndWebinarsLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\EventsAndWebinarsLivewire\Livewire\EventsDashboard;
use Livewire\Livewire;

final class EventsAndWebinarsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-crm-events-and-webinars-livewire');
        Livewire::component('module-crm-events-and-webinars-livewire::dashboard', EventsDashboard::class);
    }
}
