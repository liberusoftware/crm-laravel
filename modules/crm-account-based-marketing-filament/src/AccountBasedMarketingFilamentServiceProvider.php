<?php

declare(strict_types=1);

namespace Liberu\CRM\AccountBasedMarketingFilament;

use Illuminate\Support\ServiceProvider;

final class AccountBasedMarketingFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AccountBasedMarketingFilamentPlugin::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crm-account-based-marketing-filament');
    }
}
