<?php

declare(strict_types=1);

namespace Liberu\CRM\JourneyOrchestration\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\JourneyOrchestration\Models\JourneyEvent;
use Liberu\CRM\JourneyOrchestration\Models\JourneyRun;
use Liberu\CRM\JourneyOrchestration\Services\JourneyPolicy;

final class StopJourneyRun
{
    public function __construct(private readonly JourneyPolicy $policy) {}

    public function execute(int $teamId, int $userId, JourneyRun $run, array $input): JourneyEvent
    {
        abort_unless($run->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['reason' => ['required', 'in:response,suppressed,frequency_cap,goal,manual'], 'payload' => ['nullable', 'array']])->validate();
        $run->update(['status' => 'stopped', 'stopped_at' => now(), 'stop_reason' => $data['reason']]);

        return JourneyEvent::query()->create(['team_id' => $teamId, 'journey_id' => $run->journey_id, 'run_id' => $run->id, 'kind' => 'run_stopped', 'status' => 'recorded', 'payload' => $data['payload'] ?? null]);
    }
}
