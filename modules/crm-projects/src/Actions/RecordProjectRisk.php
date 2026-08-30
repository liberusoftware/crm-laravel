<?php

declare(strict_types=1);

namespace Liberu\CRM\Projects\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\Projects\Models\Project;
use Liberu\CRM\Projects\Models\ProjectRisk;
use Liberu\CRM\Projects\Services\ProjectPolicy;

final class RecordProjectRisk
{
    public function __construct(private readonly ProjectPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): ProjectRisk
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['project_id' => ['required', 'integer'], 'title' => ['required', 'string', 'max:255'], 'description' => ['nullable', 'string'], 'severity' => ['required', 'in:low,medium,high,critical'], 'mitigation' => ['nullable', 'array']])->validate();
        Project::query()->where('team_id', $teamId)->findOrFail($data['project_id']);

        return ProjectRisk::query()->create(['team_id' => $teamId, ...$data]);
    }
}
