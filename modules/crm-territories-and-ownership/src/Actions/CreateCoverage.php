<?php

declare(strict_types=1);

namespace Liberu\CRM\TerritoriesAndOwnership\Actions;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\TerritoriesAndOwnership\Models\TerritoryCoverage;
use Liberu\CRM\TerritoriesAndOwnership\Models\TerritoryRule;
use Liberu\CRM\TerritoriesAndOwnership\Services\TerritoryPolicy;

final class CreateCoverage
{
    public function execute(int $teamId, int $actorId, array $data): TerritoryCoverage
    {
        if (! app(TerritoryPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }

        $data = validator($data, [
            'rule_id' => ['required', 'integer', 'min:1'],
            'covered_user_id' => ['required', 'integer', 'min:1'],
            'substitute_user_id' => ['required', 'integer', 'different:covered_user_id', 'min:1'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
        ])->validate();

        $rule = TerritoryRule::query()->where('team_id', $teamId)->findOrFail($data['rule_id']);
        foreach (['covered_user_id', 'substitute_user_id'] as $field) {
            if (! app(TerritoryPolicy::class)->isTeamMember($teamId, (int) $data[$field], [], true)) {
                throw ValidationException::withMessages([$field => 'The user must belong to this team.']);
            }
        }

        $startsAt = Carbon::parse($data['starts_at']);
        $endsAt = Carbon::parse($data['ends_at']);
        $overlap = TerritoryCoverage::query()->where('team_id', $teamId)->where('rule_id', $rule->id)->where('covered_user_id', $data['covered_user_id'])->where('starts_at', '<', $endsAt)->where('ends_at', '>', $startsAt)->exists();

        if ($overlap) {
            throw ValidationException::withMessages(['starts_at' => 'Coverage overlaps an existing period.']);
        }

        return DB::transaction(fn (): TerritoryCoverage => TerritoryCoverage::query()->create([
            'team_id' => $teamId,
            'rule_id' => $rule->id,
            'covered_user_id' => $data['covered_user_id'],
            'substitute_user_id' => $data['substitute_user_id'],
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
        ]));
    }
}
