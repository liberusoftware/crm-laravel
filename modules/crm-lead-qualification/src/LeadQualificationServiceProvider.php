<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadQualification;

use Illuminate\Support\ServiceProvider;

final class LeadQualificationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
