<?php

declare(strict_types=1);

namespace Liberu\CRM\AutomationPack;

use Illuminate\Support\ServiceProvider;

final class AutomationPackServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
