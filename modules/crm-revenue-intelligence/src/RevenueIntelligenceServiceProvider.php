<?php

declare(strict_types=1);

namespace Liberu\CRM\RevenueIntelligence;

use Illuminate\Support\ServiceProvider;

final class RevenueIntelligenceServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
