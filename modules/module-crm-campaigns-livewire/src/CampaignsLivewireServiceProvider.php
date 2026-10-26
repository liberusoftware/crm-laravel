<?php

declare(strict_types=1);

namespace Liberu\CRM\CampaignsLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\CampaignsLivewire\Components\CampaignBrowser;
use Livewire\Livewire;

final class CampaignsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('crm-campaigns::campaign-browser', CampaignBrowser::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-campaigns');
    }
}
