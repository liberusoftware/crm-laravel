<?php

declare(strict_types=1);

namespace Liberu\CRM\EmailMarketingLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\EmailMarketingLivewire\Livewire\CampaignDashboard;
use Livewire\Livewire;

final class EmailMarketingLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-crm-email-marketing-livewire');
        Livewire::component('module-crm-email-marketing-livewire::dashboard', CampaignDashboard::class);
    }
}
