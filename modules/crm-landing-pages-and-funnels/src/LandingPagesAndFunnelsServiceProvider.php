<?php

declare(strict_types=1);

namespace Liberu\CRM\LandingPagesAndFunnels;

use Illuminate\Support\ServiceProvider;

final class LandingPagesAndFunnelsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
