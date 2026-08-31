<?php

declare(strict_types=1);

namespace Liberu\CRM\ConsentAndPreferences\Filament;

use Illuminate\Support\ServiceProvider;

final class ConsentAndPreferencesFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ConsentAndPreferencesFilamentPlugin::class);
    }
}
