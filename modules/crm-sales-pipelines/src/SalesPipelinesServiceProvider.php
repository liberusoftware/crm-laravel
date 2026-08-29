<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesPipelines;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\SalesPipelines\Services\PipelineAudit;

final class SalesPipelinesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PipelineAudit::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
