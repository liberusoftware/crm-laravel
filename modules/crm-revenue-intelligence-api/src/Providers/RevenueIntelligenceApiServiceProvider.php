<?php

declare(strict_types=1);

namespace Liberu\CRM\RevenueIntelligenceApi\Providers;

use Illuminate\Support\ServiceProvider;

final class RevenueIntelligenceApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
    }
}
