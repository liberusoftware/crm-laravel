<?php

declare(strict_types=1);

namespace Liberu\Foundation\CurrencyContextFilament\Pages;

use Filament\Pages\Page;

final class Overview extends Page
{
    protected string $view = 'currency-context-filament::overview';

    protected static ?string $title = 'Currency Context';
}
