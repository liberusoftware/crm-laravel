<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadQualification\Queries;

use Liberu\CRM\LeadQualification\Models\QualifiedLead;

final class LeadQualificationQuery
{
    public function forTeam(int $teamId)
    {
        return QualifiedLead::query()->where('team_id', $teamId)->with('events')->latest();
    }
}
