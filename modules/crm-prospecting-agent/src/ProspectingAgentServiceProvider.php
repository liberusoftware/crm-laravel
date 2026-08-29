<?php

declare(strict_types=1);

namespace Liberu\CRM\ProspectingAgent;

use Illuminate\Support\ServiceProvider;

final class ProspectingAgentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
