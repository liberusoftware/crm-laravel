<?php

declare(strict_types=1);

namespace Liberu\CRM\Projects\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\Projects\Models\Project;
use Liberu\CRM\Projects\Models\ProjectTask;
use Liberu\CRM\Projects\Services\ProjectPolicy;

final class CreateProjectTask
{
    public function __construct(private readonly ProjectPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): ProjectTask
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['project_id' => ['required', 'integer'], 'milestone_id' => ['nullable', 'integer'], 'owner_id' => ['nullable', 'integer'], 'depends_on_id' => ['nullable', 'integer'], 'name' => ['required', 'string', 'max:255'], 'description' => ['nullable', 'string'], 'due_at' => ['nullable', 'date']])->validate();
        Project::query()->where('team_id', $teamId)->findOrFail($data['project_id']);
        if (isset($data['depends_on_id'])) {
            ProjectTask::query()->where('team_id', $teamId)->where('project_id', $data['project_id'])->findOrFail($data['depends_on_id']);
        }

        return ProjectTask::query()->create(['team_id' => $teamId, ...$data]);
    }
}
