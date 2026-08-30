<?php

declare(strict_types=1);

namespace Liberu\CRM\AffiliateManagement\Actions;

use Liberu\CRM\AffiliateManagement\Models\Affiliate;

final class ApproveAffiliate
{
    public function execute(int $teamId, Affiliate $affiliate): Affiliate
    {
        abort_unless((int) $affiliate->team_id === $teamId, 404);
        abort_unless($affiliate->status === 'applicant', 422);
        $affiliate->update(['status' => 'active']);

        return $affiliate->fresh();
    }
}
