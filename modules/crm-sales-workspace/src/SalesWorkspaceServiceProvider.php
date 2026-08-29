<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesWorkspace;

use Illuminate\Support\ServiceProvider;

final class SalesWorkspaceServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
