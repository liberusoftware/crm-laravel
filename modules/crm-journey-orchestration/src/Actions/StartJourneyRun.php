<?php

declare(strict_types=1);

namespace Liberu\CRM\JourneyOrchestration\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\JourneyOrchestration\Models\Journey;
use Liberu\CRM\JourneyOrchestration\Models\JourneyRun;
use Liberu\CRM\JourneyOrchestration\Services\JourneyPolicy;

final class StartJourneyRun
{
    public function __construct(private readonly JourneyPolicy $policy) {}

    public function execute(int $teamId, int $userId, Journey $journey, array $input): JourneyRun
    {
        abort_unless($journey->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);
        abort_unless($journey->status === 'published', 422);
        $data = Validator::make($input, ['subject_id' => ['required', 'integer'], 'current_step' => ['nullable', 'string'], 'next_at' => ['nullable', 'date'], 'context' => ['nullable', 'array']])->validate();

        return JourneyRun::query()->firstOrCreate(['team_id' => $teamId, 'journey_id' => $journey->id, 'subject_id' => $data['subject_id']], ['current_step' => $data['current_step'] ?? null, 'next_at' => $data['next_at'] ?? null, 'context' => $data['context'] ?? null]);
    }
}
