<?php

declare(strict_types=1);

namespace Liberu\CRM\PartnerRelationshipManagement\Livewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\PartnerRelationshipManagement\Livewire\Components\PartnersDashboard;
use Livewire\Livewire;

final class PartnerRelationshipManagementLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-crm-partner-relationship-management::dashboard', PartnersDashboard::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-partner-relationship-management-livewire');
    }
}
