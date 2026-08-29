<?php

declare(strict_types=1);

namespace Liberu\CRM\ChannelSales;

use Illuminate\Support\ServiceProvider;

final class ChannelSalesServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
