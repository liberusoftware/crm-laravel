<?php

declare(strict_types=1);

namespace Liberu\CRM\WorkManagement\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\CRM\WorkManagement\Filament\Resources\WorkItemResource;
use Liberu\CRM\WorkManagement\Filament\Resources\WorkQueueResource;

final class WorkManagementFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-work-management';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([WorkItemResource::class, WorkQueueResource::class]);
    }

    public function boot(Panel $panel): void {}
}
