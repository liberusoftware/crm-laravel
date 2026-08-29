<?php

declare(strict_types=1);

namespace Liberu\CRM\ResourcePlanning\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\CRM\ResourcePlanning\Filament\Resources\PlanningResource;

final class ResourcePlanningFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-resource-planning';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([PlanningResource::class]);
    }

    public function boot(Panel $panel): void {}
}
