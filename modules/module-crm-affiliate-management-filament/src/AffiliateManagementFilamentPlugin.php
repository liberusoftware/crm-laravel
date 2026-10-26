<?php

declare(strict_types=1);

namespace Liberu\CRM\AffiliateManagementFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\CRM\AffiliateManagementFilament\Resources\AffiliateResource;

final class AffiliateManagementFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-affiliate-management';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([AffiliateResource::class]);
    }

    public function boot(Panel $panel): void {}
}
