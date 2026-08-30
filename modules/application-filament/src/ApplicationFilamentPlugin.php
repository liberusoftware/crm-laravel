<?php

declare(strict_types=1);

namespace Liberu\Foundation\ApplicationFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Foundation\ApplicationFilament\Pages\Overview;

final class ApplicationFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'application-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->pages([Overview::class]);
    }

    public function boot(Panel $panel): void {}
}
