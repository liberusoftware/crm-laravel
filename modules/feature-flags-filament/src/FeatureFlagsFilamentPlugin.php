<?php

declare(strict_types=1);

namespace Liberu\Foundation\FeatureFlagsFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Foundation\FeatureFlagsFilament\Pages\Overview;

final class FeatureFlagsFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'feature-flags-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->pages([Overview::class]);
    }

    public function boot(Panel $panel): void {}
}
