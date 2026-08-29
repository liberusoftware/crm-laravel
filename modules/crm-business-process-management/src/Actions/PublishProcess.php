<?php

declare(strict_types=1);

namespace Liberu\CRM\BusinessProcessManagement\Actions;

use Liberu\CRM\BusinessProcessManagement\Models\Process;

final class PublishProcess
{
    public function execute(int $teamId, int $actorId, Process $process): Process
    {
        abort_unless((int) $process->getAttribute('team_id') === $teamId, 404);
        abort_unless($process->getAttribute('status') === 'draft', 422);
        $process->update(['status' => 'active']);

        return $process->fresh();
    }
}
