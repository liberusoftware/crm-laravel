<?php

declare(strict_types=1);

namespace Liberu\Foundation\ApplicationFilament\Pages;

use Filament\Pages\Page;

final class Overview extends Page
{
    protected string $view = 'application-filament::overview';

    protected static ?string $title = 'Application Core';
}
