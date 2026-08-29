<?php

declare(strict_types=1);

namespace Liberu\CRM\LearningAndCourses\Queries;

use Liberu\CRM\LearningAndCourses\Models\LearningCourse;
use Liberu\CRM\LearningAndCourses\Models\LearningRecord;

final class LearningQuery
{
    public function forTeam(int $teamId)
    {
        return LearningCourse::query()->where('team_id', $teamId)->latest();
    }

    public function records(int $teamId, int $enrollmentId)
    {
        return LearningRecord::query()->where('team_id', $teamId)->where('enrollment_id', $enrollmentId)->latest();
    }
}
