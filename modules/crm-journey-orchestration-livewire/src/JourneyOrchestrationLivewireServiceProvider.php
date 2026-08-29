<?php

declare(strict_types=1);

namespace Liberu\CRM\JourneyOrchestrationLivewire;

use Illuminate\Support\ServiceProvider;

final class JourneyOrchestrationLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-crm-journey-orchestration');
    }
}
