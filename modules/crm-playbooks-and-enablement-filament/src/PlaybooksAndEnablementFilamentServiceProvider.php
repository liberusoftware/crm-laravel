<?php

declare(strict_types=1);

namespace Liberu\CRM\PlaybooksAndEnablement\Filament;

use Illuminate\Support\ServiceProvider;

final class PlaybooksAndEnablementFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PlaybooksAndEnablementFilamentPlugin::class);
    }
}
