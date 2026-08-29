<?php

declare(strict_types=1);

namespace Liberu\CRM\QuotasAndIncentives\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\QuotasAndIncentives\Models\Quota;
use Liberu\CRM\QuotasAndIncentives\Services\QuotaPolicy;

final class UpdateQuota
{
    public function __construct(private readonly QuotaPolicy $policy) {}

    public function execute(int $teamId, int $userId, int $quotaId, array $input): Quota
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, [
            'user_id' => ['nullable', 'integer'],
            'territory' => ['nullable', 'string', 'max:255'],
            'period_start' => ['required', 'date'],
            'period_end' => ['required', 'date', 'after_or_equal:period_start'],
            'target' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'ramp' => ['nullable', 'array'],
        ])->validate();
        $quota = Quota::query()->where('team_id', $teamId)->findOrFail($quotaId);
        $quota->update($data);

        return $quota->refresh();
    }
}
