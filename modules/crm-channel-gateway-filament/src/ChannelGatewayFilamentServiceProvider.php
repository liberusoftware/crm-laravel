<?php

declare(strict_types=1);

namespace Liberu\CRM\ChannelGatewayFilament;

use Illuminate\Support\ServiceProvider;

final class ChannelGatewayFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ChannelGatewayFilamentPlugin::class);
    }
}
