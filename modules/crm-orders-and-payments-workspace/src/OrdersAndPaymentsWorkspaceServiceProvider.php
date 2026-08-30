<?php

declare(strict_types=1);

namespace Liberu\CRM\OrdersAndPaymentsWorkspace;

use Illuminate\Support\ServiceProvider;

final class OrdersAndPaymentsWorkspaceServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
