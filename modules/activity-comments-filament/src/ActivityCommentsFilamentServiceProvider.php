<?php

declare(strict_types=1);

namespace Liberu\Foundation\ActivityCommentsFilament;

use Illuminate\Support\ServiceProvider;

final class ActivityCommentsFilamentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'activity-comments-filament');
    }
}
