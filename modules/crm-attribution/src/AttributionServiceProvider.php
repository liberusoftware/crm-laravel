<?php

declare(strict_types=1);

namespace Liberu\CRM\Attribution;

use Illuminate\Support\ServiceProvider;

final class AttributionServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
