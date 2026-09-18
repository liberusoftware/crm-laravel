<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesEngagement\Actions;

use Illuminate\Validation\ValidationException;
use Liberu\CRM\SalesEngagement\Models\EngagementSequence;
use Liberu\CRM\SalesEngagement\Services\EngagementPolicy;

final class CreateSequence
{
    public function execute(int $teamId, int $actorId, array $data): EngagementSequence
    {
        if (! app(EngagementPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }

        $data = validator($data, ['name' => ['required', 'string', 'max:255'], 'timezone' => ['required', 'timezone'], 'throttle' => ['nullable', 'array'], 'stop_rules' => ['nullable', 'array:reply,meeting'], 'stop_rules.reply' => ['sometimes', 'boolean'], 'stop_rules.meeting' => ['sometimes', 'boolean'], 'experiment' => ['nullable', 'array']])->validate();

        return EngagementSequence::query()->create(array_merge($data, ['team_id' => $teamId, 'status' => 'draft']));
    }
}
