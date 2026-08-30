<?php

declare(strict_types=1);

namespace Liberu\Foundation\SearchFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Foundation\SearchFilament\Pages\Overview;

final class SearchFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'search-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->pages([Overview::class]);
    }

    public function boot(Panel $panel): void {}
}
