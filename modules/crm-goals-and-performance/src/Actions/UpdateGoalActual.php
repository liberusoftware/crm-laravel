<?php

declare(strict_types=1);

namespace Liberu\CRM\GoalsAndPerformance\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\GoalsAndPerformance\Models\PerformanceEvent;
use Liberu\CRM\GoalsAndPerformance\Models\PerformanceGoal;
use Liberu\CRM\GoalsAndPerformance\Services\PerformancePolicy;

final class UpdateGoalActual
{
    public function __construct(private readonly PerformancePolicy $policy) {}

    public function execute(int $teamId, int $userId, PerformanceGoal $goal, array $input): PerformanceEvent
    {
        abort_unless($goal->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['kind' => ['required', 'in:activity,outcome,scorecard,coaching'], 'value' => ['nullable', 'numeric'], 'notes' => ['nullable', 'string'], 'payload' => ['nullable', 'array']])->validate();
        $event = PerformanceEvent::query()->create(['team_id' => $teamId, 'goal_id' => $goal->id, 'actor_id' => $userId, ...$data]);
        if ($event->value !== null) {
            $goal->increment('actual', (float) $event->value);
        }

        return $event;
    }
}
