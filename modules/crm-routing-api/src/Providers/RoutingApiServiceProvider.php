<?php

declare(strict_types=1);

namespace Liberu\CRM\RoutingApi\Providers;

use Illuminate\Support\ServiceProvider;

final class RoutingApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
    }
}
