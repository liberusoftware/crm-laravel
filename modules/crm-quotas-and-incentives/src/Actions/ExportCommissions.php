<?php

declare(strict_types=1);

namespace Liberu\CRM\QuotasAndIncentives\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\QuotasAndIncentives\Models\CommissionExport;
use Liberu\CRM\QuotasAndIncentives\Services\QuotaPolicy;

final class ExportCommissions
{
    public function __construct(private readonly QuotaPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): CommissionExport
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['period' => ['required', 'string', 'max:50'], 'format' => ['required', 'in:csv,json']])->validate();

        return CommissionExport::query()->create(['team_id' => $teamId, ...$data]);
    }
}
