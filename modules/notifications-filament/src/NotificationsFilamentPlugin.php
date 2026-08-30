<?php

declare(strict_types=1);

namespace Liberu\Foundation\NotificationsFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Foundation\NotificationsFilament\Pages\Overview;

final class NotificationsFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'notifications-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->pages([Overview::class]);
    }

    public function boot(Panel $panel): void {}
}
