<?php

declare(strict_types=1);

namespace Liberu\CRM\ChannelSalesLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\ChannelSalesLivewire\Components\OpportunityBrowser;
use Livewire\Livewire;

final class ChannelSalesLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('crm-channel-sales::opportunity-browser', OpportunityBrowser::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-channel-sales');
    }
}
