<?php

declare(strict_types=1);

namespace Liberu\Foundation\ObservabilityFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Foundation\ObservabilityFilament\Pages\Overview;

final class ObservabilityFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'observability-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->pages([Overview::class]);
    }

    public function boot(Panel $panel): void {}
}
