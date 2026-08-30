<?php

declare(strict_types=1);

namespace Liberu\Foundation\JetstreamBridgeFilament\Pages;

use Filament\Pages\Page;

final class Overview extends Page
{
    protected string $view = 'jetstream-bridge-filament::overview';

    protected static ?string $title = 'Jetstream Bridge';
}
