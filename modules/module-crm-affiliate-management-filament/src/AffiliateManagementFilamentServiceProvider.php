<?php

declare(strict_types=1);

namespace Liberu\CRM\AffiliateManagementFilament;

use Illuminate\Support\ServiceProvider;

final class AffiliateManagementFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AffiliateManagementFilamentPlugin::class);
    }
}
