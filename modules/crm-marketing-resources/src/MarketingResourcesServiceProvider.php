<?php

declare(strict_types=1);

namespace Liberu\CRM\MarketingResources;

use Illuminate\Support\ServiceProvider;

final class MarketingResourcesServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
