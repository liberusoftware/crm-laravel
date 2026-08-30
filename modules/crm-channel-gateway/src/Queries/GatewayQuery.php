<?php

declare(strict_types=1);

namespace Liberu\CRM\ChannelGateway\Queries;

use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\ChannelGateway\Models\GatewayChannel;
use Liberu\CRM\ChannelGateway\Models\GatewayDelivery;

final class GatewayQuery
{
    public function channels(int $teamId): Builder
    {
        return GatewayChannel::query()->where('team_id', $teamId)->latest();
    }

    public function deliveries(int $teamId): Builder
    {
        return GatewayDelivery::query()->where('team_id', $teamId)->latest();
    }
}
