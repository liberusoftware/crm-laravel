<?php

declare(strict_types=1);

namespace Liberu\Foundation\ProfilesFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Foundation\ProfilesFilament\Pages\Overview;

final class ProfilesFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'profiles-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->pages([Overview::class]);
    }

    public function boot(Panel $panel): void {}
}
