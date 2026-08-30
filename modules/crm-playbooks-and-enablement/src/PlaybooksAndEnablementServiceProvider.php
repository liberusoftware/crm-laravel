<?php

declare(strict_types=1);

namespace Liberu\CRM\PlaybooksAndEnablement;

use Illuminate\Support\ServiceProvider;

final class PlaybooksAndEnablementServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
