<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadQualification\Services;

use Illuminate\Support\Carbon;
use Liberu\CRM\LeadQualification\Models\LeadQualification;

final class QualificationReport
{
    /** @return array<string, mixed> */
    public function summarize(int $teamId, Carbon $from, Carbon $until): array
    {
        $query = LeadQualification::query()->where('team_id', $teamId)->whereBetween('created_at', [$from, $until]);
        $total = (int) (clone $query)->count();

        return ['total' => $total, 'converted' => (int) (clone $query)->where('qualification_status', 'converted')->count(), 'disqualified' => (int) (clone $query)->where('qualification_status', 'disqualified')->count(), 'nurturing' => (int) (clone $query)->where('qualification_status', 'nurturing')->count(), 'average_score' => $total === 0 ? 0.0 : round((float) $query->avg('total_score'), 2)];
    }
}
