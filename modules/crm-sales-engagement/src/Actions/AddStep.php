<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesEngagement\Actions;

use Illuminate\Validation\ValidationException;
use Liberu\CRM\SalesEngagement\Models\EngagementSequence;
use Liberu\CRM\SalesEngagement\Models\EngagementStep;
use Liberu\CRM\SalesEngagement\Services\EngagementPolicy;

final class AddStep
{
    public function execute(int $teamId, int $actorId, array $data): EngagementStep
    {
        if (! app(EngagementPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }validator($data, ['sequence_id' => ['required', 'integer'], 'position' => ['required', 'integer', 'min:1'], 'channel' => ['required', 'in:email,sms,call,social,task'], 'delay_minutes' => ['nullable', 'integer', 'min:0'], 'template' => ['nullable', 'string'], 'snippet' => ['nullable', 'array']])->validate();
        if (! EngagementSequence::query()->where('team_id', $teamId)->whereKey($data['sequence_id'])->exists()) {
            throw ValidationException::withMessages(['sequence_id' => 'Sequence does not belong to this team.']);
        }

        return EngagementStep::query()->create($data);
    }
}
