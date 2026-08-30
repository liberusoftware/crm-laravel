<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesWorkspace\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\CRM\SalesWorkspace\Filament\Resources\WorkspaceItemResource;

final class SalesWorkspaceFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-sales-workspace';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([WorkspaceItemResource::class]);
    }

    public function boot(Panel $panel): void {}
}
