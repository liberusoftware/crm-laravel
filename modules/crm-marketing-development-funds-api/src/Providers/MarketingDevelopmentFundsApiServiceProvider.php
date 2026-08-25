<?php

declare(strict_types=1);

namespace Liberu\CRM\MarketingDevelopmentFundsApi\Providers;

use Illuminate\Support\ServiceProvider;

final class MarketingDevelopmentFundsApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
