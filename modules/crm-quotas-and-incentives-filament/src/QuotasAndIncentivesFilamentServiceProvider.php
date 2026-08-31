<?php

declare(strict_types=1);

namespace Liberu\CRM\QuotasAndIncentives\Filament;

use Illuminate\Support\ServiceProvider;

final class QuotasAndIncentivesFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(QuotasAndIncentivesFilamentPlugin::class);
    }
}
