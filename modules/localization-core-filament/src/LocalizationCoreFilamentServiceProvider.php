<?php

declare(strict_types=1);

namespace Liberu\Foundation\LocalizationCoreFilament;

use Illuminate\Support\ServiceProvider;

final class LocalizationCoreFilamentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'localization-core-filament');
    }
}
