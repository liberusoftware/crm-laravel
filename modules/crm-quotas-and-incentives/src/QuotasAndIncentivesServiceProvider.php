<?php

declare(strict_types=1);

namespace Liberu\CRM\QuotasAndIncentives;

use Illuminate\Support\ServiceProvider;

final class QuotasAndIncentivesServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
