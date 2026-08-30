<?php

declare(strict_types=1);

namespace Liberu\Foundation\NotificationsFilament;

use Illuminate\Support\ServiceProvider;

final class NotificationsFilamentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'notifications-filament');
    }
}
