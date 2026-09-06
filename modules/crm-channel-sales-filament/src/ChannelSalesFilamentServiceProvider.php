<?php

declare(strict_types=1);

namespace Liberu\CRM\ChannelSalesFilament;

use Illuminate\Support\ServiceProvider;

final class ChannelSalesFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ChannelSalesFilamentPlugin::class);
    }
}
