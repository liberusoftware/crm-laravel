<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadQualificationLivewire;

use Illuminate\Support\ServiceProvider;

final class LeadQualificationLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-lead-qualification-livewire');
    }
}
