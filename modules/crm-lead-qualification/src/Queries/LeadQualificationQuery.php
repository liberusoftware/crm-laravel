<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadQualification\Queries;

use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\LeadQualification\Models\QualifiedLead;

final class LeadQualificationQuery
{
    /** @return Builder<QualifiedLead> */
    public function forTeam(int $teamId): Builder
    {
        return QualifiedLead::query()->where('team_id', $teamId)->with('events')->latest();
    }
}
