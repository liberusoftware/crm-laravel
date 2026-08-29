<?php

declare(strict_types=1);

namespace Liberu\CRM\Memberships;

use Illuminate\Support\ServiceProvider;

final class MembershipsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
