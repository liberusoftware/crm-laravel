<?php

declare(strict_types=1);

namespace Liberu\Foundation\ActivityCommentsFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Foundation\ActivityCommentsFilament\Pages\Overview;

final class ActivityCommentsFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'activity-comments-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->pages([Overview::class]);
    }

    public function boot(Panel $panel): void {}
}
