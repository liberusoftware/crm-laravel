<?php

declare(strict_types=1);

namespace Liberu\CRM\CPQ;

use Illuminate\Support\ServiceProvider;

final class CpqServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
