<?php

declare(strict_types=1);

namespace Liberu\CRM\ResourcePlanning\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\ResourcePlanning\Models\ResourceSkill;
use Liberu\CRM\ResourcePlanning\Services\ResourcePlanningPolicy;

final class UpsertSkill
{
    public function __construct(private readonly ResourcePlanningPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): ResourceSkill
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['id' => ['nullable', 'integer'], 'name' => ['required', 'string', 'max:255'], 'description' => ['nullable', 'string'], 'proficiency' => ['required', 'integer', 'between:1,5'], 'metadata' => ['nullable', 'array']])->validate();
        $id = $data['id'] ?? null;
        unset($data['id']);

        return ResourceSkill::query()->updateOrCreate(['id' => $id, 'team_id' => $teamId], ['team_id' => $teamId, ...$data]);
    }
}
