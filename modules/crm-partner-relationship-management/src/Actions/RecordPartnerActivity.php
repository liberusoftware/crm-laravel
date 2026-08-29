<?php

declare(strict_types=1);

namespace Liberu\CRM\PartnerRelationshipManagement\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\PartnerRelationshipManagement\Models\PartnerAccount;
use Liberu\CRM\PartnerRelationshipManagement\Models\PartnerActivity;
use Liberu\CRM\PartnerRelationshipManagement\Services\PartnerPolicy;

final class RecordPartnerActivity
{
    public function __construct(private readonly PartnerPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): PartnerActivity
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['partner_id' => ['required', 'integer'], 'kind' => ['required', 'in:recruitment,onboarding,enablement,certification,deal,referral'], 'value' => ['nullable', 'numeric', 'min:0'], 'payload' => ['nullable', 'array']])->validate();
        PartnerAccount::query()->where('team_id', $teamId)->findOrFail($data['partner_id']);

        return PartnerActivity::query()->create(['team_id' => $teamId, ...$data, 'occurred_at' => now()]);
    }
}
