<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesEngagement\Actions;

use Illuminate\Validation\ValidationException;
use Liberu\CRM\SalesEngagement\Models\EngagementSequence;
use Liberu\CRM\SalesEngagement\Services\EngagementPolicy;

final class UpdateSequence
{
    public function execute(int $teamId, int $actorId, int $sequenceId, array $data): EngagementSequence
    {
        if (! app(EngagementPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }

        validator($data, [
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:draft,active,paused'],
            'timezone' => ['required', 'timezone'],
        ])->validate();

        $sequence = EngagementSequence::query()->where('team_id', $teamId)->findOrFail($sequenceId);
        $sequence->update($data);

        return $sequence;
    }
}
