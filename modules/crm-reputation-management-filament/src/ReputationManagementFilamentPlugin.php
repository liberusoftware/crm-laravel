<?php

declare(strict_types=1);

namespace Liberu\CRM\ReputationManagement\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\CRM\ReputationManagement\Filament\Resources\ReputationResource;

final class ReputationManagementFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-reputation-management';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([ReputationResource::class]);
    }

    public function boot(Panel $panel): void {}
}
