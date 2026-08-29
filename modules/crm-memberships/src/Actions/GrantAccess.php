<?php

declare(strict_types=1);

namespace Liberu\CRM\Memberships\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\Memberships\Models\MembershipGrant;
use Liberu\CRM\Memberships\Models\MembershipPlan;
use Liberu\CRM\Memberships\Services\MembershipPolicy;

final class GrantAccess
{
    public function __construct(private readonly MembershipPolicy $policy) {}

    public function execute(int $teamId, int $userId, MembershipPlan $plan, array $input): MembershipGrant
    {
        abort_unless($plan->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['resource_id' => ['required', 'integer'], 'member_id' => ['required', 'integer'], 'status' => ['nullable', 'in:active,suspended,expired'], 'starts_at' => ['required', 'date'], 'ends_at' => ['nullable', 'date'], 'renewal_reference' => ['nullable', 'string', 'max:255'], 'metadata' => ['nullable', 'array']])->validate();

        return MembershipGrant::query()->create(['team_id' => $teamId, 'plan_id' => $plan->id, ...$data]);
    }
}
