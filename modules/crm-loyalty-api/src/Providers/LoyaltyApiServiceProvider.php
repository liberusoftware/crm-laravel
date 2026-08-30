<?php

declare(strict_types=1);

namespace Liberu\CRM\LoyaltyApi\Providers;

use Illuminate\Support\ServiceProvider;

final class LoyaltyApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
