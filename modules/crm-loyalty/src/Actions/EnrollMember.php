<?php

declare(strict_types=1);

namespace Liberu\CRM\Loyalty\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\Loyalty\Models\LoyaltyMember;
use Liberu\CRM\Loyalty\Models\LoyaltyProgram;
use Liberu\CRM\Loyalty\Services\LoyaltyPolicy;

final class EnrollMember
{
    public function __construct(private readonly LoyaltyPolicy $policy) {}

    public function execute(int $teamId, int $userId, LoyaltyProgram $program, array $input): LoyaltyMember
    {
        abort_unless($program->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['member_id' => ['required', 'integer'], 'tier' => ['nullable', 'string', 'max:100']])->validate();

        return LoyaltyMember::query()->firstOrCreate(['team_id' => $teamId, 'program_id' => $program->id, 'member_id' => $data['member_id']], ['tier' => $data['tier'] ?? 'standard']);
    }
}
