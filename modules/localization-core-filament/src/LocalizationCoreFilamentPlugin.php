<?php

declare(strict_types=1);

namespace Liberu\Foundation\LocalizationCoreFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Foundation\LocalizationCoreFilament\Pages\Overview;

final class LocalizationCoreFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'localization-core-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->pages([Overview::class]);
    }

    public function boot(Panel $panel): void {}
}
