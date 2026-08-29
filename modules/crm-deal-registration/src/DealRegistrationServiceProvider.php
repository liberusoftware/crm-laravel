<?php

declare(strict_types=1);

namespace Liberu\CRM\DealRegistration;

use Illuminate\Support\ServiceProvider;

final class DealRegistrationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
