<?php

declare(strict_types=1);

namespace Liberu\CRM\ChannelGateway;

use Illuminate\Support\ServiceProvider;

final class ChannelGatewayServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
