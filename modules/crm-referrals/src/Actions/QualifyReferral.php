<?php

declare(strict_types=1);

namespace Liberu\CRM\Referrals\Actions;

use Liberu\CRM\Referrals\Events\ReferralStatusChanged;
use Liberu\CRM\Referrals\Models\Referral;
use Liberu\CRM\Referrals\Services\ReferralPolicy;

final class QualifyReferral
{
    public function __construct(private readonly ReferralPolicy $policy) {}

    public function execute(int $teamId, int $userId, int $referralId, string $status): Referral
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        abort_unless(in_array($status, ['qualified', 'rejected', 'converted'], true), 422);
        $referral = Referral::query()->where('team_id', $teamId)->findOrFail($referralId);
        $referral->update(['status' => $status, 'qualified_at' => $status === 'qualified' ? now() : $referral->qualified_at]);
        event(new ReferralStatusChanged($referral, $status));

        return $referral->refresh();
    }
}
