<?php

declare(strict_types=1);

namespace Liberu\CRM\Referrals\Actions;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Liberu\CRM\Referrals\Events\ReferralStatusChanged;
use Liberu\CRM\Referrals\Models\Referral;
use Liberu\CRM\Referrals\Models\ReferralProgram;
use Liberu\CRM\Referrals\Services\ReferralPolicy;

final class CreateReferral
{
    public function __construct(private readonly ReferralPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): Referral
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['program_id' => ['required', 'integer'], 'advocate_id' => ['nullable', 'integer'], 'prospect_email' => ['required', 'email', 'max:255'], 'prospect_name' => ['nullable', 'string', 'max:255'], 'source' => ['nullable', 'string', 'max:100']])->validate();
        $program = ReferralProgram::query()->where('team_id', $teamId)->where('active', true)->findOrFail($data['program_id']);
        $duplicate = Referral::query()->where('team_id', $teamId)->where('program_id', $program->id)->where('prospect_email', $data['prospect_email'])->whereNotIn('status', ['rejected', 'cancelled'])->exists();
        abort_if($duplicate, 422, 'A referral already exists for this prospect.');
        $referral = Referral::query()->create(['team_id' => $teamId, ...$data, 'code' => $program->code_prefix.'-'.Str::upper(Str::random(10)), 'attributed_at' => now()]);
        event(new ReferralStatusChanged($referral, 'pending'));

        return $referral;
    }
}
