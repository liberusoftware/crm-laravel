<?php

declare(strict_types=1);

namespace Liberu\Foundation\DeveloperExperienceFilament;

use Illuminate\Support\ServiceProvider;

final class DeveloperExperienceFilamentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'developer-experience-filament');
    }
}
