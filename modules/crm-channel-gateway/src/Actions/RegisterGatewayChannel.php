<?php

declare(strict_types=1);

namespace Liberu\CRM\ChannelGateway\Actions;

use Liberu\CRM\ChannelGateway\Models\GatewayChannel;

final class RegisterGatewayChannel
{
    public function execute(int $teamId, string $key, string $kind, string $provider, array $configuration = []): GatewayChannel
    {
        abort_unless($key !== '' && in_array($kind, ['email', 'sms', 'mms', 'whatsapp', 'web_chat', 'social', 'push'], true) && $provider !== '', 422);

        return GatewayChannel::query()->updateOrCreate(['team_id' => $teamId, 'key' => $key], ['kind' => $kind, 'provider' => $provider, 'configuration' => $configuration, 'status' => 'active', 'health' => ['status' => 'unknown']]);
    }
}
