<?php

declare(strict_types=1);

namespace Liberu\CRM\CampaignsFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\CRM\CampaignsFilament\Resources\CampaignResource;

final class CampaignsFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-campaigns';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([CampaignResource::class]);
    }

    public function boot(Panel $panel): void {}
}
