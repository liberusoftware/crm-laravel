<?php

declare(strict_types=1);

namespace Liberu\CRM\Prospecting;

use Illuminate\Support\ServiceProvider;

final class ProspectingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
