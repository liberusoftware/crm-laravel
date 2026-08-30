<?php

declare(strict_types=1);

namespace Liberu\Foundation\SchedulerQueuesLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class SchedulerQueuesLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'scheduler-queues-livewire');
        Livewire::component('scheduler-queues-livewire-overview', Liberu\Foundation\SchedulerQueuesLivewire\Livewire\Overview::class);
    }
}
