<?php

declare(strict_types=1);

namespace Liberu\CRM\JourneyOrchestration\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\JourneyOrchestration\Models\Journey;
use Liberu\CRM\JourneyOrchestration\Services\JourneyPolicy;

final class CreateJourney
{
    public function __construct(private readonly JourneyPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): Journey
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['slug' => ['required', 'string', 'max:255'], 'name' => ['required', 'string', 'max:255'], 'trigger_type' => ['required', 'in:event,scheduled'], 'definition' => ['required', 'array'], 'controls' => ['nullable', 'array']])->validate();

        return Journey::query()->create(['team_id' => $teamId, ...$data]);
    }
}
