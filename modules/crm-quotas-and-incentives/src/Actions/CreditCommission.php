<?php

declare(strict_types=1);

namespace Liberu\CRM\QuotasAndIncentives\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\QuotasAndIncentives\Events\CommissionCredited;
use Liberu\CRM\QuotasAndIncentives\Models\CommissionCredit;
use Liberu\CRM\QuotasAndIncentives\Models\CommissionPlan;
use Liberu\CRM\QuotasAndIncentives\Services\QuotaPolicy;

final class CreditCommission
{
    public function __construct(private readonly QuotaPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): CommissionCredit
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['user_id' => ['required', 'integer'], 'plan_id' => ['required', 'integer'], 'quota_id' => ['nullable', 'integer'], 'source_type' => ['required', 'string', 'max:255'], 'source_id' => ['required', 'integer'], 'amount' => ['required', 'numeric', 'min:0'], 'idempotency_key' => ['required', 'string', 'max:255']])->validate();
        $existing = CommissionCredit::query()->where('idempotency_key', $data['idempotency_key'])->first();
        if ($existing !== null) {
            abort_unless((int) $existing->team_id === $teamId, 403);

            return $existing;
        } $plan = CommissionPlan::query()->where('team_id', $teamId)->where('active', true)->findOrFail($data['plan_id']);
        $credit = CommissionCredit::query()->create(['team_id' => $teamId, ...$data, 'commission' => ((float) $data['amount']) * (float) $plan->rate]);
        event(new CommissionCredited($credit));

        return $credit;
    }
}
