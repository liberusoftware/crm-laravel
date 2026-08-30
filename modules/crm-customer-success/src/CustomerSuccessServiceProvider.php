<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerSuccess;

use Illuminate\Support\ServiceProvider;

final class CustomerSuccessServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
