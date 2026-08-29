<?php

declare(strict_types=1);

namespace Liberu\CRM\SandboxAndReleaseManagement\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\CRM\SandboxAndReleaseManagement\Filament\Resources\ReleaseResource;

final class SandboxAndReleaseManagementFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-sandbox-and-release-management';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([ReleaseResource::class]);
    }

    public function boot(Panel $panel): void {}
}
