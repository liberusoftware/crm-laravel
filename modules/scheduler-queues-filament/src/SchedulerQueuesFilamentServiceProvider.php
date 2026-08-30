<?php

declare(strict_types=1);

namespace Liberu\Foundation\SchedulerQueuesFilament;

use Illuminate\Support\ServiceProvider;

final class SchedulerQueuesFilamentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'scheduler-queues-filament');
    }
}
