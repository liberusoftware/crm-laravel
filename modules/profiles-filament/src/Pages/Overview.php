<?php

declare(strict_types=1);

namespace Liberu\Foundation\ProfilesFilament\Pages;

use Filament\Pages\Page;

final class Overview extends Page
{
    protected string $view = 'profiles-filament::overview';

    protected static ?string $title = 'Profiles';
}
