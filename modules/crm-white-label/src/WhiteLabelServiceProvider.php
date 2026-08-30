<?php

declare(strict_types=1);

namespace Liberu\CRM\WhiteLabel;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\WhiteLabel\Services\WhiteLabelAudit;
use Liberu\CRM\WhiteLabel\Services\WhiteLabelPolicy;

final class WhiteLabelServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(WhiteLabelPolicy::class);
        $this->app->singleton(WhiteLabelAudit::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
