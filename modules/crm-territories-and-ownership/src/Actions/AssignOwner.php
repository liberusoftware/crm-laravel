<?php

declare(strict_types=1);

namespace Liberu\CRM\TerritoriesAndOwnership\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\TerritoriesAndOwnership\Models\OwnershipHistory;
use Liberu\CRM\TerritoriesAndOwnership\Services\TerritoryPolicy;

final class AssignOwner
{
    public function execute(int $teamId, int $actorId, string $subjectType, int $subjectId, ?int $previousOwnerId, int $ownerId, string $reason): OwnershipHistory
    {
        if (! app(TerritoryPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }

        $data = validator(compact('subjectType', 'subjectId', 'ownerId', 'reason'), ['subjectType' => ['required', 'string', 'max:100'], 'subjectId' => ['required', 'integer', 'min:1'], 'ownerId' => ['required', 'integer', 'min:1'], 'reason' => ['required', 'string', 'max:255']])->validate();

        if (! app(TerritoryPolicy::class)->isTeamMember($teamId, (int) $data['ownerId'], [], true)) {
            throw ValidationException::withMessages(['ownerId' => 'The owner must belong to this team.']);
        }

        return DB::transaction(fn (): OwnershipHistory => OwnershipHistory::query()->create(['team_id' => $teamId, 'subject_type' => $data['subjectType'], 'subject_id' => $data['subjectId'], 'previous_owner_id' => $previousOwnerId, 'owner_id' => $data['ownerId'], 'reason' => $data['reason'], 'actor_id' => $actorId]));
    }
}
