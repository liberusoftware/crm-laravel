<?php

declare(strict_types=1);

namespace Liberu\Foundation\FilesMediaFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Foundation\FilesMediaFilament\Pages\Overview;

final class FilesMediaFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'files-media-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->pages([Overview::class]);
    }

    public function boot(Panel $panel): void {}
}
