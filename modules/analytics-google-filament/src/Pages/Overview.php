<?php

declare(strict_types=1);

namespace Liberu\Foundation\AnalyticsGoogleFilament\Pages;

use Filament\Pages\Page;

final class Overview extends Page
{
    protected string $view = 'analytics-google-filament::overview';

    protected static ?string $title = 'Google Analytics';
}
