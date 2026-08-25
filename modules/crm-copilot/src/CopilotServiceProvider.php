<?php

declare(strict_types=1);

namespace Liberu\CRM\Copilot;

use Illuminate\Support\ServiceProvider;

final class CopilotServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(Contracts\AutomationGateway::class, Services\NullAutomationGateway::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
