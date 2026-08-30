<?php

declare(strict_types=1);

namespace Liberu\CRM\Memberships\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\Memberships\Models\MembershipPlan;
use Liberu\CRM\Memberships\Services\MembershipPolicy;

final class CreatePlan
{
    public function __construct(private readonly MembershipPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): MembershipPlan
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['name' => ['required', 'string', 'max:255'], 'status' => ['nullable', 'in:draft,active,archived'], 'interval' => ['required', 'in:monthly,quarterly,annual,one_time'], 'price' => ['required', 'numeric', 'min:0'], 'currency' => ['required', 'string', 'size:3'], 'metadata' => ['nullable', 'array']])->validate();

        return MembershipPlan::query()->create(['team_id' => $teamId, ...$data]);
    }
}
