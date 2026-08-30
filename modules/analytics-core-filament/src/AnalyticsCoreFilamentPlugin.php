<?php

declare(strict_types=1);

namespace Liberu\Foundation\AnalyticsCoreFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Foundation\AnalyticsCoreFilament\Pages\Overview;

final class AnalyticsCoreFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'analytics-core-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->pages([Overview::class]);
    }

    public function boot(Panel $panel): void {}
}
