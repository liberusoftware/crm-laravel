<?php

declare(strict_types=1);

namespace Liberu\CRM\ChannelSalesFilament\Resources\ChannelOpportunityResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\ChannelSales\Actions\RegisterChannelOpportunity;
use Liberu\CRM\ChannelSalesFilament\Resources\ChannelOpportunityResource;

final class CreateChannelOpportunity extends CreateRecord
{
    protected static string $resource = ChannelOpportunityResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $user = auth()->user();

        return app(RegisterChannelOpportunity::class)->execute((int) $user?->getAttribute('current_team_id'), (int) $user?->getKey(), (string) $data['partner_key'], (string) $data['opportunity_key'], (float) $data['amount'], (float) $data['commission_rate']);
    }
}
