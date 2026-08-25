<?php

declare(strict_types=1);

namespace Tests\Feature\ChannelGateway;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\ChannelGateway\Actions\QueueGatewayDelivery;
use Liberu\CRM\ChannelGateway\Actions\RegisterGatewayChannel;
use Liberu\CRM\ChannelGateway\Actions\UpdateGatewayHealth;
use Tests\TestCase;

final class ChannelGatewayModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_provider_neutral_channels_delivery_idempotency_and_health_are_scoped(): void
    {
        $channel = app(RegisterGatewayChannel::class)->execute(7, 'email-main', 'email', 'smtp', ['region' => 'eu']);
        $delivery = app(QueueGatewayDelivery::class)->execute(7, $channel, 'idem-1', 'a@example.test', 'Hello');
        $same = app(QueueGatewayDelivery::class)->execute(7, $channel, 'idem-1', 'a@example.test', 'Hello again');
        $healthy = app(UpdateGatewayHealth::class)->execute(7, $channel, true);
        $this->assertSame($delivery->id, $same->id);
        $this->assertSame('queued', $same->status);
        $this->assertSame('healthy', $healthy->health['status']);
    }
}
