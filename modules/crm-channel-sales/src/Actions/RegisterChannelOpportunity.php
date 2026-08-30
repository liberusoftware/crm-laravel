<?php

declare(strict_types=1);

namespace Liberu\CRM\ChannelSales\Actions;

use Liberu\CRM\ChannelSales\Models\ChannelOpportunity;

final class RegisterChannelOpportunity
{
    public function execute(int $teamId, int $ownerId, string $partnerKey, string $opportunityKey, float $amount, float $commissionRate, array $pricingReference = []): ChannelOpportunity
    {
        abort_unless($partnerKey !== '' && $opportunityKey !== '' && $amount >= 0 && $commissionRate >= 0 && $commissionRate <= 100, 422);

        return ChannelOpportunity::query()->create(['team_id' => $teamId, 'owner_id' => $ownerId, 'partner_key' => $partnerKey, 'opportunity_key' => $opportunityKey, 'amount' => $amount, 'commission_rate' => $commissionRate, 'pricing_reference' => $pricingReference, 'forecast' => ['amount' => $amount, 'probability' => 0.1]]);
    }
}
