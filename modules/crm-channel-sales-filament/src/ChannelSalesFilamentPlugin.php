<?php

declare(strict_types=1);

namespace Liberu\CRM\ChannelSalesFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\CRM\ChannelSalesFilament\Resources\ChannelOpportunityResource;

final class ChannelSalesFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-channel-sales';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([ChannelOpportunityResource::class]);
    }

    public function boot(Panel $panel): void {}
}
