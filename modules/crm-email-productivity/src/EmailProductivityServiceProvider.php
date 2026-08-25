<?php

declare(strict_types=1);

namespace Liberu\CRM\EmailProductivity;

use Illuminate\Support\ServiceProvider;

final class EmailProductivityServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
