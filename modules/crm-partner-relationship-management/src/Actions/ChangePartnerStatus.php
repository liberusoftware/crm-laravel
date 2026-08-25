<?php

declare(strict_types=1);

namespace Liberu\CRM\PartnerRelationshipManagement\Actions;

use Liberu\CRM\PartnerRelationshipManagement\Events\PartnerStatusChanged;
use Liberu\CRM\PartnerRelationshipManagement\Models\PartnerAccount;
use Liberu\CRM\PartnerRelationshipManagement\Services\PartnerPolicy;

final class ChangePartnerStatus
{
    public function __construct(private readonly PartnerPolicy $policy) {}

    public function execute(int $teamId, int $userId, int $partnerId, string $status): PartnerAccount
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        abort_unless(in_array($status, ['prospect', 'onboarding', 'active', 'suspended', 'inactive'], true), 422);
        $partner = PartnerAccount::query()->where('team_id', $teamId)->findOrFail($partnerId);
        $partner->update(['status' => $status]);
        event(new PartnerStatusChanged($partner, $status));

        return $partner->refresh();
    }
}
