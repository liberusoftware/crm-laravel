<?php

declare(strict_types=1);

namespace Liberu\CRM\JourneyOrchestration\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\JourneyOrchestration\Models\Journey;
use Liberu\CRM\JourneyOrchestration\Services\JourneyPolicy;

final class PublishJourney
{
    public function __construct(private readonly JourneyPolicy $policy) {}

    public function execute(int $teamId, int $userId, Journey $journey, array $input): Journey
    {
        abort_unless($journey->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['status' => ['required', 'in:published,paused,archived'], 'version' => ['nullable', 'integer', 'min:1']])->validate();
        $journey->fill(['status' => $data['status'], 'version' => $data['version'] ?? $journey->version + 1])->save();

        return $journey->refresh();
    }
}
