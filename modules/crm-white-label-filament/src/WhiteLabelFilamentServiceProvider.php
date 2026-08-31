<?php

declare(strict_types=1);

namespace Liberu\CRM\WhiteLabel\Filament;

use Illuminate\Support\ServiceProvider;

final class WhiteLabelFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(WhiteLabelFilamentPlugin::class);
    }
}
