<?php

declare(strict_types=1);

namespace Liberu\CRM\ChannelGatewayLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\ChannelGatewayLivewire\Components\GatewayBrowser;
use Livewire\Livewire;

final class ChannelGatewayLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('crm-channel-gateway::gateway-browser', GatewayBrowser::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-channel-gateway');
    }
}
