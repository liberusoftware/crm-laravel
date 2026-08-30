<?php

declare(strict_types=1);

namespace Liberu\Foundation\TwoFactorAuthenticationFilament;

use Illuminate\Support\ServiceProvider;

final class TwoFactorAuthenticationFilamentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'two-factor-authentication-filament');
    }
}
