<?php

declare(strict_types=1);

namespace Liberu\Foundation\IntegrationsFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Foundation\IntegrationsFilament\Pages\Overview;

final class IntegrationsFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'integrations-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->pages([Overview::class]);
    }

    public function boot(Panel $panel): void {}
}
