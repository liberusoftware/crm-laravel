<?php

declare(strict_types=1);

namespace Liberu\CRM\ServiceAgent;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\ServiceAgent\Services\AgentAudit;

final class ServiceAgentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AgentAudit::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
