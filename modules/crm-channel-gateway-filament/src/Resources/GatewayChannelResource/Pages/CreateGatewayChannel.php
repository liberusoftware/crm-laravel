<?php

declare(strict_types=1);

namespace Liberu\CRM\ChannelGatewayFilament\Resources\GatewayChannelResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\ChannelGateway\Actions\RegisterGatewayChannel;
use Liberu\CRM\ChannelGatewayFilament\Resources\GatewayChannelResource;

final class CreateGatewayChannel extends CreateRecord
{
    protected static string $resource = GatewayChannelResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $user = auth()->user();

        return app(RegisterGatewayChannel::class)->execute((int) $user?->getAttribute('current_team_id'), (string) $data['key'], (string) $data['kind'], (string) $data['provider'], (array) ($data['configuration'] ?? []));
    }
}
