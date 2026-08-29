<?php

declare(strict_types=1);

namespace Liberu\CRM\Scheduling\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\CRM\Scheduling\Filament\Resources\BookingResource;

final class SchedulingFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-scheduling';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([BookingResource::class]);
    }

    public function boot(Panel $panel): void {}
}
