<?php

declare(strict_types=1);

namespace Liberu\CRM\GoalsAndPerformanceLivewire;

use Illuminate\Support\ServiceProvider;

final class GoalsAndPerformanceLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-crm-goals-and-performance');
    }
}
