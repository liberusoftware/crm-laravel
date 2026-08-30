<?php

declare(strict_types=1);

namespace Liberu\Foundation\TwoFactorAuthenticationFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Foundation\TwoFactorAuthenticationFilament\Pages\Overview;

final class TwoFactorAuthenticationFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'two-factor-authentication-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->pages([Overview::class]);
    }

    public function boot(Panel $panel): void {}
}
