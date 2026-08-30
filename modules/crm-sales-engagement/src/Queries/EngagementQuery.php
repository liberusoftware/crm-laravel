<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesEngagement\Queries;

use Liberu\CRM\SalesEngagement\Models\EngagementSequence;
use Liberu\CRM\SalesEngagement\Models\EngagementTask;
use Liberu\CRM\SalesEngagement\Models\Enrollment;

final class EngagementQuery
{
    public function sequences(int $teamId)
    {
        return EngagementSequence::query()->where('team_id', $teamId)->latest();
    }

    public function enrollments(int $teamId)
    {
        return Enrollment::query()->where('team_id', $teamId)->latest();
    }

    public function tasks(int $teamId)
    {
        return EngagementTask::query()->where('team_id', $teamId)->latest();
    }
}
