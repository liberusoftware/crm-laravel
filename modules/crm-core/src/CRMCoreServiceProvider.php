<?php

declare(strict_types=1);

namespace Liberu\CRM\Core;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\Core\Contracts\RecordRepository;
use Liberu\CRM\Core\Services\EloquentRecordRepository;

final class CRMCoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(RecordRepository::class, EloquentRecordRepository::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
