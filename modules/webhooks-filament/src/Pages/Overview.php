<?php

declare(strict_types=1);

namespace Liberu\Foundation\WebhooksFilament\Pages;

use Filament\Pages\Page;

final class Overview extends Page
{
    protected string $view = 'webhooks-filament::overview';

    protected static ?string $title = 'Webhooks';
}
