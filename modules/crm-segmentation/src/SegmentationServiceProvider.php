<?php

declare(strict_types=1);

namespace Liberu\CRM\Segmentation;

use Illuminate\Support\ServiceProvider;
use Liberu\CRM\Segmentation\Services\SegmentationAudit;

final class SegmentationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SegmentationAudit::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
