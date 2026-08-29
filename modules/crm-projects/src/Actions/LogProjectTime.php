<?php

declare(strict_types=1);

namespace Liberu\CRM\Projects\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\Projects\Models\ProjectTime;
use Liberu\CRM\Projects\Services\ProjectPolicy;

final class LogProjectTime
{
    public function __construct(private readonly ProjectPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): ProjectTime
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['project_id' => ['required', 'integer'], 'task_id' => ['nullable', 'integer'], 'hours' => ['required', 'numeric', 'gt:0', 'max:24'], 'worked_at' => ['required', 'date'], 'notes' => ['nullable', 'string']])->validate();

        return ProjectTime::query()->create(['team_id' => $teamId, 'user_id' => $userId, ...$data]);
    }
}
