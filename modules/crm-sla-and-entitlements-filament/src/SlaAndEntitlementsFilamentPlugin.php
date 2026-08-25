<?php

declare(strict_types=1);

namespace Liberu\CRM\SlaAndEntitlements\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\CRM\SlaAndEntitlements\Filament\Resources\SlaCaseResource;
use Liberu\CRM\SlaAndEntitlements\Filament\Resources\SlaContractResource;

final class SlaAndEntitlementsFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-sla-and-entitlements';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([SlaContractResource::class, SlaCaseResource::class]);
    }

    public function boot(Panel $panel): void {}
}
