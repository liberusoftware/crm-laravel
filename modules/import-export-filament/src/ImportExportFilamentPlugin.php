<?php

declare(strict_types=1);

namespace Liberu\Foundation\ImportExportFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Foundation\ImportExportFilament\Pages\Overview;

final class ImportExportFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'import-export-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->pages([Overview::class]);
    }

    public function boot(Panel $panel): void {}
}
