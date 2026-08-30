<?php

declare(strict_types=1);

namespace Liberu\Foundation\ApplicationFilament;

use Illuminate\Support\ServiceProvider;

final class ApplicationFilamentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'application-filament');
    }
}
