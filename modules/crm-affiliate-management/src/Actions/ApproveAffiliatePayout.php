<?php

declare(strict_types=1);

namespace Liberu\CRM\AffiliateManagement\Actions;

use Illuminate\Support\Carbon;
use Liberu\CRM\AffiliateManagement\Models\AffiliatePayout;

final class ApproveAffiliatePayout
{
    public function execute(int $teamId, int $actorId, AffiliatePayout $payout): AffiliatePayout
    {
        abort_unless((int) $payout->team_id === $teamId, 404);
        abort_unless($payout->status === 'pending' && (float) $payout->amount > 0, 422);
        $payout->update(['status' => 'approved', 'approved_by' => $actorId, 'approved_at' => Carbon::now()]);

        return $payout->fresh();
    }
}
