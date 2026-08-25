<?php

declare(strict_types=1);

namespace Liberu\CRM\AccountBasedMarketingApi;

use Illuminate\Support\ServiceProvider;

final class AccountBasedMarketingApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
