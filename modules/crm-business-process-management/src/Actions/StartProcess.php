<?php

declare(strict_types=1);

namespace Liberu\CRM\BusinessProcessManagement\Actions;

use Illuminate\Support\Carbon;
use Liberu\CRM\BusinessProcessManagement\Models\Process;
use Liberu\CRM\BusinessProcessManagement\Models\ProcessRun;

final class StartProcess
{
    /** @param array<string,mixed> $context */
    public function execute(int $teamId, int $actorId, Process $process, array $context = []): ProcessRun
    {
        abort_unless((int) $process->getAttribute('team_id') === $teamId, 404);
        abort_unless($process->getAttribute('status') === 'active', 422);
        $steps = (array) $process->getAttribute('definition')['steps'];
        $first = array_values($steps)[0] ?? null;
        $firstKey = is_array($first) ? (string) ($first['key'] ?? '') : (string) $first;
        abort_unless($firstKey !== '', 422);
        $run = ProcessRun::query()->create(['team_id' => $teamId, 'process_id' => $process->getKey(), 'actor_id' => $actorId, 'status' => 'running', 'current_step' => $firstKey, 'context' => $context, 'started_at' => Carbon::now()]);
        $run->events()->create(['team_id' => $teamId, 'actor_id' => $actorId, 'type' => 'started', 'payload' => ['step' => $run->getAttribute('current_step')]]);

        return $run;
    }
}
