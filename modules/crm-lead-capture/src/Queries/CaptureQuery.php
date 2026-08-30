<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadCapture\Queries;

use Liberu\CRM\LeadCapture\Models\CapturedLead;

final class CaptureQuery
{
    public function forTeam(int $teamId)
    {
        return CapturedLead::query()->where('team_id', $teamId)->with('events')->latest();
    }
}
