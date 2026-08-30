<?php

declare(strict_types=1);

namespace Liberu\CRM\PartnerRelationshipManagement\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\CRM\PartnerRelationshipManagement\Filament\Resources\PartnerResource;

final class PartnerRelationshipManagementFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-partner-relationship-management';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([PartnerResource::class]);
    }

    public function boot(Panel $panel): void {}
}
