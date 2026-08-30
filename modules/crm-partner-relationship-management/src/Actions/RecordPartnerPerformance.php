<?php

declare(strict_types=1);

namespace Liberu\CRM\PartnerRelationshipManagement\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\PartnerRelationshipManagement\Models\PartnerAccount;
use Liberu\CRM\PartnerRelationshipManagement\Models\PartnerPerformance;
use Liberu\CRM\PartnerRelationshipManagement\Services\PartnerPolicy;

final class RecordPartnerPerformance
{
    public function __construct(private readonly PartnerPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): PartnerPerformance
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['partner_id' => ['required', 'integer'], 'period_start' => ['required', 'date'], 'period_end' => ['required', 'date', 'after_or_equal:period_start'], 'revenue' => ['required', 'numeric', 'min:0'], 'deals' => ['required', 'integer', 'min:0'], 'score' => ['required', 'numeric', 'between:0,100']])->validate();
        PartnerAccount::query()->where('team_id', $teamId)->findOrFail($data['partner_id']);

        return PartnerPerformance::query()->create(['team_id' => $teamId, ...$data]);
    }
}
