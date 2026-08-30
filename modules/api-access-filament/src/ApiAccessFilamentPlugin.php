<?php

declare(strict_types=1);

namespace Liberu\Foundation\ApiAccessFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Foundation\ApiAccessFilament\Pages\Overview;

final class ApiAccessFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'api-access-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->pages([Overview::class]);
    }

    public function boot(Panel $panel): void {}
}
