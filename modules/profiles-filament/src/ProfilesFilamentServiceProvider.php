<?php

declare(strict_types=1);

namespace Liberu\Foundation\ProfilesFilament;

use Illuminate\Support\ServiceProvider;

final class ProfilesFilamentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'profiles-filament');
    }
}
