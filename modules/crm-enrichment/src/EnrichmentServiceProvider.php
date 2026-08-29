<?php

declare(strict_types=1);

namespace Liberu\CRM\Enrichment;

use Illuminate\Support\ServiceProvider;

final class EnrichmentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
