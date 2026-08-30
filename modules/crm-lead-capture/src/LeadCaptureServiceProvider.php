<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadCapture;

use Illuminate\Support\ServiceProvider;

final class LeadCaptureServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
