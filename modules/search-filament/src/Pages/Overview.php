<?php

declare(strict_types=1);

namespace Liberu\Foundation\SearchFilament\Pages;

use Filament\Pages\Page;

final class Overview extends Page
{
    protected string $view = 'search-filament::overview';

    protected static ?string $title = 'Search';
}
