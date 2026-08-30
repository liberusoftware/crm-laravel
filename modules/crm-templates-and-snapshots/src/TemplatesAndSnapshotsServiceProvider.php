<?php

declare(strict_types=1);

namespace Liberu\CRM\TemplatesAndSnapshots;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\TemplatesAndSnapshots\Services\SnapshotPolicy;

final class TemplatesAndSnapshotsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SnapshotPolicy::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
