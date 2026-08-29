<?php

declare(strict_types=1);

namespace Liberu\CRM\ChannelGatewayApi;

use Illuminate\Support\ServiceProvider;

final class ChannelGatewayApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
