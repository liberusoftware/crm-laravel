<?php

declare(strict_types=1);

namespace Liberu\CRM\Prospecting\Filament;

use Illuminate\Support\ServiceProvider;

final class ProspectingFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ProspectingFilamentPlugin::class);
    }
}
