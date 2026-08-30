<?php

declare(strict_types=1);

namespace Liberu\CRM\ChannelGateway\Actions;

use Liberu\CRM\ChannelGateway\Models\GatewayChannel;
use Liberu\CRM\ChannelGateway\Models\GatewayDelivery;

final class QueueGatewayDelivery
{
    public function execute(int $teamId, GatewayChannel $channel, string $idempotencyKey, string $address, string $body, array $metadata = []): GatewayDelivery
    {
        abort_unless((int) $channel->team_id === $teamId && $channel->status === 'active' && $idempotencyKey !== '' && $address !== '' && trim($body) !== '', 422);

        return GatewayDelivery::query()->firstOrCreate(['team_id' => $teamId, 'idempotency_key' => $idempotencyKey], ['channel_id' => $channel->id, 'address' => $address, 'body' => $body, 'status' => 'queued', 'metadata' => $metadata]);
    }
}
