<?php

declare(strict_types=1);

namespace Liberu\CRM\AdvertisingApi;

use Illuminate\Support\ServiceProvider;

final class AdvertisingApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
