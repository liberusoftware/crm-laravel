<?php

declare(strict_types=1);

namespace Liberu\Foundation\CurrencyContextFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Foundation\CurrencyContextFilament\Pages\Overview;

final class CurrencyContextFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'currency-context-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->pages([Overview::class]);
    }

    public function boot(Panel $panel): void {}
}
