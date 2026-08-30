<?php

declare(strict_types=1);

namespace Liberu\Foundation\ObservabilityFilament\Pages;

use Filament\Pages\Page;

final class Overview extends Page
{
    protected string $view = 'observability-filament::overview';

    protected static ?string $title = 'Observability';
}
