<?php

declare(strict_types=1);

namespace Liberu\CRM\Contracts;

use Illuminate\Support\ServiceProvider;

final class ContractsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
