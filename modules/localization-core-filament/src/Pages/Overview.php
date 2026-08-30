<?php

declare(strict_types=1);

namespace Liberu\Foundation\LocalizationCoreFilament\Pages;

use Filament\Pages\Page;

final class Overview extends Page
{
    protected string $view = 'localization-core-filament::overview';

    protected static ?string $title = 'Localization';
}
