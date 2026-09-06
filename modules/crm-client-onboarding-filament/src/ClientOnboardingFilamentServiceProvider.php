<?php

declare(strict_types=1);

namespace Liberu\CRM\ClientOnboardingFilament;

use Illuminate\Support\ServiceProvider;

final class ClientOnboardingFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ClientOnboardingFilamentPlugin::class);
    }
}
