<?php

declare(strict_types=1);

namespace Liberu\CRM\Activities\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\CRM\Activities\Filament\Resources\ActivityGoalResource;
use Liberu\CRM\Activities\Filament\Resources\ActivityResource;

final class ActivitiesFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-activities';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([ActivityResource::class, ActivityGoalResource::class]);
    }

    public function boot(Panel $panel): void {}
}
