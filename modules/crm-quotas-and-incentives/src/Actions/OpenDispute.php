<?php

declare(strict_types=1);

namespace Liberu\CRM\QuotasAndIncentives\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\QuotasAndIncentives\Models\CommissionDispute;
use Liberu\CRM\QuotasAndIncentives\Services\QuotaPolicy;

final class OpenDispute
{
    public function __construct(private readonly QuotaPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): CommissionDispute
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['credit_id' => ['required', 'integer'], 'reason' => ['required', 'string', 'max:5000']])->validate();

        return CommissionDispute::query()->create(['team_id' => $teamId, 'opened_by' => $userId, ...$data]);
    }
}
