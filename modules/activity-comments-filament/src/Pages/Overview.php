<?php

declare(strict_types=1);

namespace Liberu\Foundation\ActivityCommentsFilament\Pages;

use Filament\Pages\Page;

final class Overview extends Page
{
    protected string $view = 'activity-comments-filament::overview';

    protected static ?string $title = 'Activity and Comments';
}
