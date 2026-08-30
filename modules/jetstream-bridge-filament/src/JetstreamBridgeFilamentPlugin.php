<?php

declare(strict_types=1);

namespace Liberu\Foundation\JetstreamBridgeFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Foundation\JetstreamBridgeFilament\Pages\Overview;

final class JetstreamBridgeFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'jetstream-bridge-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->pages([Overview::class]);
    }

    public function boot(Panel $panel): void {}
}
