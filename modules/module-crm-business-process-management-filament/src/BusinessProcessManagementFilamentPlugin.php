<?php

declare(strict_types=1);

namespace Liberu\CRM\BusinessProcessManagementFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\CRM\BusinessProcessManagementFilament\Resources\ProcessResource;

final class BusinessProcessManagementFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-business-process-management';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([ProcessResource::class]);
    }

    public function boot(Panel $panel): void {}
}
