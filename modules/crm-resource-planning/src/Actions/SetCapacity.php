<?php

declare(strict_types=1);

namespace Liberu\CRM\ResourcePlanning\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\ResourcePlanning\Models\ResourceCapacity;
use Liberu\CRM\ResourcePlanning\Services\ResourcePlanningPolicy;

final class SetCapacity
{
    public function __construct(private readonly ResourcePlanningPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): ResourceCapacity
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['resource_id' => ['required', 'integer'], 'period_start' => ['required', 'date'], 'period_end' => ['required', 'date', 'after_or_equal:period_start'], 'available_hours' => ['required', 'numeric', 'min:0'], 'allocated_hours' => ['nullable', 'numeric', 'min:0'], 'metadata' => ['nullable', 'array']])->validate();

        return ResourceCapacity::query()->updateOrCreate(['team_id' => $teamId, 'resource_id' => $data['resource_id'], 'period_start' => $data['period_start'], 'period_end' => $data['period_end']], ['team_id' => $teamId, ...$data]);
    }
}
