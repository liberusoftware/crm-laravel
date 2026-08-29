<?php

declare(strict_types=1);

namespace Liberu\CRM\Referrals\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\Referrals\Models\Referral;
use Liberu\CRM\Referrals\Services\ReferralPolicy;

final class UpdateReferral
{
    public function __construct(private readonly ReferralPolicy $policy) {}

    public function execute(int $teamId, int $userId, int $referralId, array $input): Referral
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, [
            'advocate_id' => ['nullable', 'integer'],
            'prospect_email' => ['required', 'email', 'max:255'],
            'prospect_name' => ['nullable', 'string', 'max:255'],
            'source' => ['nullable', 'string', 'max:100'],
        ])->validate();
        $referral = Referral::query()->where('team_id', $teamId)->findOrFail($referralId);
        $referral->update($data);

        return $referral->refresh();
    }
}
