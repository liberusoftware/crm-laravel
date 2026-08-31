<?php

declare(strict_types=1);

namespace Liberu\CRM\PartnerRelationshipManagement\Filament;

use Illuminate\Support\ServiceProvider;

final class PartnerRelationshipManagementFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PartnerRelationshipManagementFilamentPlugin::class);
    }
}
