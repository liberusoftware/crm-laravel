<?php

declare(strict_types=1);

namespace Liberu\Foundation\TwoFactorAuthenticationFilament\Pages;

use Filament\Pages\Page;

final class Overview extends Page
{
    protected string $view = 'two-factor-authentication-filament::overview';

    protected static ?string $title = 'Two-Factor Authentication';
}
