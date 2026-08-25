<?php

declare(strict_types=1);

namespace Liberu\CRM\ChannelGateway\Actions;

use Liberu\CRM\ChannelGateway\Models\GatewayChannel;

final class UpdateGatewayHealth
{
    public function execute(int $teamId, GatewayChannel $channel, bool $healthy, ?string $failure = null): GatewayChannel
    {
        abort_unless((int) $channel->team_id === $teamId, 403);
        $channel->update(['health' => ['status' => $healthy ? 'healthy' : 'unhealthy', 'failure' => $failure, 'checked_at' => now()->toIso8601String()], 'status' => $healthy ? 'active' : 'degraded']);

        return $channel->refresh();
    }
}
