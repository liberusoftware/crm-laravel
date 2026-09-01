<?php

declare(strict_types=1);

namespace Liberu\CRM\AgencyWorkspaceFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\CRM\AgencyWorkspaceFilament\Resources\AgencyAccountResource;

final class AgencyWorkspaceFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-agency-workspace';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([AgencyAccountResource::class]);
    }

    public function boot(Panel $panel): void {}
}
