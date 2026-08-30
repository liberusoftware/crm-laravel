<?php

declare(strict_types=1);

namespace Liberu\CRM\PartnerRelationshipManagement\Queries;

use Liberu\CRM\PartnerRelationshipManagement\Models\PartnerAccount;
use Liberu\CRM\PartnerRelationshipManagement\Models\PartnerActivity;
use Liberu\CRM\PartnerRelationshipManagement\Models\PartnerContact;
use Liberu\CRM\PartnerRelationshipManagement\Models\PartnerPerformance;

final class PartnerQuery
{
    public function partners(int $teamId)
    {
        return PartnerAccount::query()->where('team_id', $teamId)->latest();
    }

    public function contacts(int $teamId)
    {
        return PartnerContact::query()->where('team_id', $teamId)->latest();
    }

    public function activities(int $teamId)
    {
        return PartnerActivity::query()->where('team_id', $teamId)->latest();
    }

    public function performance(int $teamId)
    {
        return PartnerPerformance::query()->where('team_id', $teamId)->latest();
    }
}
