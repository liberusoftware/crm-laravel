<?php

declare(strict_types=1);

namespace Liberu\CRM\AgencyWorkspace;

use Illuminate\Support\ServiceProvider;

final class AgencyWorkspaceServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
