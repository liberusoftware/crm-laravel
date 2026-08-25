<?php

declare(strict_types=1);

namespace Liberu\CRM\FieldServiceCoordination;

use Illuminate\Support\ServiceProvider;

final class FieldServiceCoordinationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
