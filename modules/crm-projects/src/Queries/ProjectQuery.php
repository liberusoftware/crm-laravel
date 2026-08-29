<?php

declare(strict_types=1);

namespace Liberu\CRM\Projects\Queries;

use Liberu\CRM\Projects\Models\Project;
use Liberu\CRM\Projects\Models\ProjectRisk;
use Liberu\CRM\Projects\Models\ProjectTask;
use Liberu\CRM\Projects\Models\ProjectTemplate;

final class ProjectQuery
{
    public function templates(int $teamId)
    {
        return ProjectTemplate::query()->where('team_id', $teamId)->latest();
    }

    public function projects(int $teamId)
    {
        return Project::query()->where('team_id', $teamId)->latest();
    }

    public function tasks(int $teamId)
    {
        return ProjectTask::query()->where('team_id', $teamId)->latest();
    }

    public function risks(int $teamId)
    {
        return ProjectRisk::query()->where('team_id', $teamId)->latest();
    }
}
