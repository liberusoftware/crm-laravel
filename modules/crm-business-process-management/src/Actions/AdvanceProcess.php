<?php

declare(strict_types=1);

namespace Liberu\CRM\BusinessProcessManagement\Actions;

use Illuminate\Support\Carbon;
use Liberu\CRM\BusinessProcessManagement\Models\ProcessRun;

final class AdvanceProcess
{
    /** @param array<string,mixed> $context */
    public function execute(int $teamId, int $actorId, ProcessRun $run, array $context = []): ProcessRun
    {
        abort_unless((int) $run->getAttribute('team_id') === $teamId, 404);
        abort_unless($run->getAttribute('status') === 'running', 422);
        $process = $run->process()->firstOrFail();
        $steps = array_values((array) $process->getAttribute('definition')['steps']);
        $current = (string) $run->getAttribute('current_step');
        $position = array_search($current, array_map(static fn (mixed $step): string => is_array($step) ? (string) ($step['key'] ?? '') : (string) $step, $steps), true);
        abort_unless($position !== false, 422);
        $next = $steps[$position + 1] ?? null;
        $payload = array_merge((array) $run->getAttribute('context'), $context);
        if ($next === null) {
            $run->update(['status' => 'completed', 'current_step' => null, 'context' => $payload, 'completed_at' => Carbon::now()]);
            $type = 'completed';
        } else {
            $nextKey = is_array($next) ? (string) ($next['key'] ?? '') : (string) $next;
            abort_unless($nextKey !== '', 422);
            $run->update(['current_step' => $nextKey, 'context' => $payload]);
            $type = 'advanced';
        }
        $run->events()->create(['team_id' => $teamId, 'actor_id' => $actorId, 'type' => $type, 'payload' => ['from' => $current, 'to' => $run->getAttribute('current_step'), 'context' => $payload]]);

        return $run->fresh();
    }
}
