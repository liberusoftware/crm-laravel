<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadCaptureLivewire;

use Illuminate\Support\ServiceProvider;

final class LeadCaptureLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-crm-lead-capture');
    }
}
