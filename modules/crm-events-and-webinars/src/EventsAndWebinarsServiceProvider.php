<?php

declare(strict_types=1);

namespace Liberu\CRM\EventsAndWebinars;

use Illuminate\Support\ServiceProvider;

final class EventsAndWebinarsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
