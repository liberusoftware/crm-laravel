<?php

declare(strict_types=1);

namespace Liberu\CRM\Referrals\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\Referrals\Models\Referral;
use Liberu\CRM\Referrals\Models\ReferralProgram;
use Liberu\CRM\Referrals\Models\ReferralReward;
use Liberu\CRM\Referrals\Services\ReferralPolicy;

final class IssueReward
{
    public function __construct(private readonly ReferralPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): ReferralReward
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['referral_id' => ['required', 'integer'], 'idempotency_key' => ['required', 'string', 'max:255']])->validate();
        $existing = ReferralReward::query()->where('idempotency_key', $data['idempotency_key'])->first();
        if ($existing !== null) {
            abort_unless((int) $existing->team_id === $teamId, 403);

            return $existing;
        } $referral = Referral::query()->where('team_id', $teamId)->where('status', 'qualified')->findOrFail($data['referral_id']);
        $program = ReferralProgram::query()->where('team_id', $teamId)->findOrFail($referral->program_id);

        return ReferralReward::query()->create(['team_id' => $teamId, 'referral_id' => $referral->id, 'amount' => $program->reward_amount, 'currency' => $program->reward_currency, 'idempotency_key' => $data['idempotency_key']]);
    }
}
