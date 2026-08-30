<?php

declare(strict_types=1);

namespace Liberu\CRM\WebIntent;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\WebIntent\Services\WebIntentAudit;
use Liberu\CRM\WebIntent\Services\WebIntentPolicy;
use Liberu\CRM\WebIntent\Services\WebIntentScorer;

final class WebIntentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(WebIntentScorer::class);
        $this->app->singleton(WebIntentPolicy::class);
        $this->app->singleton(WebIntentAudit::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
