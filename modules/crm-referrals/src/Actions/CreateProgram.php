<?php

declare(strict_types=1);

namespace Liberu\CRM\Referrals\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\Referrals\Models\ReferralProgram;
use Liberu\CRM\Referrals\Services\ReferralPolicy;

final class CreateProgram
{
    public function __construct(private readonly ReferralPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): ReferralProgram
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['name' => ['required', 'string', 'max:255'], 'code_prefix' => ['required', 'alpha_num', 'max:20'], 'reward_amount' => ['required', 'numeric', 'min:0'], 'reward_currency' => ['required', 'string', 'size:3'], 'rules' => ['nullable', 'array']])->validate();

        return ReferralProgram::query()->create(['team_id' => $teamId, ...$data]);
    }
}
