<?php

declare(strict_types=1);

namespace Liberu\CRM\ChannelGatewayFilament\Resources\GatewayChannelResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\ChannelGatewayFilament\Resources\GatewayChannelResource;

final class ListGatewayChannels extends ListRecords
{
    protected static string $resource = GatewayChannelResource::class;
}
