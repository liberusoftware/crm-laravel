<?php

declare(strict_types=1);

namespace Liberu\CRM\Loyalty\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\Loyalty\Models\LoyaltyControl;
use Liberu\CRM\Loyalty\Models\LoyaltyMember;
use Liberu\CRM\Loyalty\Services\LoyaltyPolicy;

final class SetFraudControl
{
    public function __construct(private readonly LoyaltyPolicy $policy) {}

    public function execute(int $teamId, int $userId, LoyaltyMember $member, array $input): LoyaltyControl
    {
        abort_unless($member->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['status' => ['required', 'in:open,cleared'], 'reason' => ['required', 'string', 'max:1000']])->validate();
        $member->update(['fraud_locked' => $data['status'] === 'open']);

        return LoyaltyControl::query()->create(['team_id' => $teamId, 'member_id' => $member->id, 'kind' => 'fraud', 'status' => $data['status'], 'reason' => $data['reason']]);
    }
}
