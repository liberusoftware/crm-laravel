<?php

declare(strict_types=1);

namespace Liberu\CRM\AffiliateManagement;

use Illuminate\Support\ServiceProvider;

final class AffiliateManagementServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
