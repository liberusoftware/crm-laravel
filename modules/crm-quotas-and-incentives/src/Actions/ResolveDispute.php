<?php

declare(strict_types=1);

namespace Liberu\CRM\QuotasAndIncentives\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\QuotasAndIncentives\Models\CommissionDispute;
use Liberu\CRM\QuotasAndIncentives\Services\QuotaPolicy;

final class ResolveDispute
{
    public function __construct(private readonly QuotaPolicy $policy) {}

    public function execute(int $teamId, int $userId, int $disputeId, array $input): CommissionDispute
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['resolution' => ['required', 'string', 'max:5000'], 'status' => ['required', 'in:approved,rejected']])->validate();
        $dispute = CommissionDispute::query()->where('team_id', $teamId)->findOrFail($disputeId);
        $dispute->update([...$data, 'resolved_at' => now()]);

        return $dispute->refresh();
    }
}
