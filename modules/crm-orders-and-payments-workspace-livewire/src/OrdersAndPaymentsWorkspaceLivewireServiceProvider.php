<?php

declare(strict_types=1);

namespace Liberu\CRM\OrdersAndPaymentsWorkspaceLivewire;

use Illuminate\Support\ServiceProvider;

final class OrdersAndPaymentsWorkspaceLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-crm-orders-and-payments-workspace');
    }
}
