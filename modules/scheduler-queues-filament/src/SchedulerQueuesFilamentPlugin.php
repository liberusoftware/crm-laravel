<?php

declare(strict_types=1);

namespace Liberu\Foundation\SchedulerQueuesFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\Foundation\SchedulerQueuesFilament\Pages\Overview;

final class SchedulerQueuesFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'scheduler-queues-filament';
    }

    public function register(Panel $panel): void
    {
        $panel->pages([Overview::class]);
    }

    public function boot(Panel $panel): void {}
}
