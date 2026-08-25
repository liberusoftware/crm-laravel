<?php

declare(strict_types=1);

namespace Liberu\CRM\MarketingAgent;

use Illuminate\Support\ServiceProvider;

final class MarketingAgentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
