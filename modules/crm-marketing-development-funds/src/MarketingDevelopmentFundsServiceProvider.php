<?php

declare(strict_types=1);

namespace Liberu\CRM\MarketingDevelopmentFunds;

use Illuminate\Support\ServiceProvider;

final class MarketingDevelopmentFundsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
