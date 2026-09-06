<?php

declare(strict_types=1);

namespace Liberu\CRM\CampaignsFilament;

use Illuminate\Support\ServiceProvider;

final class CampaignsFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CampaignsFilamentPlugin::class);
    }
}
