<?php

declare(strict_types=1);

namespace Liberu\CRM\ChannelGatewayFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\CRM\ChannelGatewayFilament\Resources\GatewayChannelResource;

final class ChannelGatewayFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-channel-gateway';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([GatewayChannelResource::class]);
    }

    public function boot(Panel $panel): void {}
}
