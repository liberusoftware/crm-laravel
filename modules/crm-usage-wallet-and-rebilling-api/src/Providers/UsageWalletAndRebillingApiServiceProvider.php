<?php

declare(strict_types=1);

namespace Liberu\CRM\UsageWalletAndRebillingApi\Providers;

use Illuminate\Support\ServiceProvider;

final class UsageWalletAndRebillingApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
    }
}
