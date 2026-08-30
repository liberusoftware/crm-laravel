<?php

declare(strict_types=1);

namespace Liberu\Foundation\AnalyticsGoogleFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Foundation\AnalyticsGoogleFilament\Pages\Overview;

final class AnalyticsGoogleFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'analytics-google-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->pages([Overview::class]);
    }

    public function boot(Panel $panel): void {}
}
