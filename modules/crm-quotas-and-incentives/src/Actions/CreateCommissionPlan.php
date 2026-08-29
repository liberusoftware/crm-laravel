<?php

declare(strict_types=1);

namespace Liberu\CRM\QuotasAndIncentives\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\QuotasAndIncentives\Models\CommissionPlan;
use Liberu\CRM\QuotasAndIncentives\Services\QuotaPolicy;

final class CreateCommissionPlan
{
    public function __construct(private readonly QuotaPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): CommissionPlan
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['name' => ['required', 'string', 'max:255'], 'rate' => ['required', 'numeric', 'min:0'], 'accelerators' => ['nullable', 'array']])->validate();

        return CommissionPlan::query()->create(['team_id' => $teamId, ...$data]);
    }
}
