<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerSelfService;

use Illuminate\Support\ServiceProvider;

final class CustomerSelfServiceServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
