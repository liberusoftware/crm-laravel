<?php

declare(strict_types=1);

namespace Liberu\CRM\PartnerRelationshipManagement;

use Illuminate\Support\ServiceProvider;

final class PartnerRelationshipManagementServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
