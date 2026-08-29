<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerDataPlatformApi;

use Illuminate\Support\ServiceProvider;

final class CustomerDataPlatformApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
