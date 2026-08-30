<?php

declare(strict_types=1);

namespace Liberu\Foundation\AnalyticsMetaFilament\Pages;

use Filament\Pages\Page;

final class Overview extends Page
{
    protected string $view = 'analytics-meta-filament::overview';

    protected static ?string $title = 'Meta Server-Side Tracking';
}
