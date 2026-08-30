<?php

declare(strict_types=1);

namespace Liberu\Foundation\SchedulerQueuesFilament\Pages;

use Filament\Pages\Page;

final class Overview extends Page
{
    protected string $view = 'scheduler-queues-filament::overview';

    protected static ?string $title = 'Scheduler and Queues';
}
