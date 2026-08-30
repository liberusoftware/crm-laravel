<?php

declare(strict_types=1);

namespace Liberu\CRM\Communities;

use Illuminate\Support\ServiceProvider;

final class CommunitiesServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
