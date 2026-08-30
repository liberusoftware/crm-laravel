<?php

declare(strict_types=1);

namespace Liberu\Foundation\DeveloperExperienceFilament\Pages;

use Filament\Pages\Page;

final class Overview extends Page
{
    protected string $view = 'developer-experience-filament::overview';

    protected static ?string $title = 'Developer Experience';
}
