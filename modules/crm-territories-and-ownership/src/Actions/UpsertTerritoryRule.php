<?php

declare(strict_types=1);

namespace Liberu\CRM\TerritoriesAndOwnership\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\TerritoriesAndOwnership\Models\TerritoryRule;
use Liberu\CRM\TerritoriesAndOwnership\Services\TerritoryPolicy;

final class UpsertTerritoryRule
{
    public function execute(int $teamId, int $actorId, array $data): TerritoryRule
    {
        if (! app(TerritoryPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }

        $data = validator($data, ['name' => ['required', 'string', 'max:255'], 'book_of_business' => ['nullable', 'string', 'max:255'], 'criteria' => ['nullable', 'array'], 'members' => ['required', 'array'], 'members.*' => ['integer', 'distinct'], 'capacity' => ['nullable', 'integer', 'min:1'], 'active' => ['boolean']])->validate();
        $members = array_map('intval', $data['members']);
        $invalidMembers = array_diff($members, $this->memberIds($teamId));

        if ($invalidMembers !== []) {
            throw ValidationException::withMessages(['members' => 'All territory members must belong to the team.']);
        }

        $data['members'] = $members;

        return DB::transaction(function () use ($teamId, $data): TerritoryRule {
            $rule = TerritoryRule::query()->updateOrCreate(['team_id' => $teamId, 'name' => $data['name']], array_merge($data, ['team_id' => $teamId]));

            return $rule->fresh();
        });
    }

    private function memberIds(int $teamId): array
    {
        $owner = DB::table('teams')->where('id', $teamId)->value('user_id');
        $members = DB::table('team_user')->where('team_id', $teamId)->where(function ($query): void {
            $query->whereNull('status')->orWhere('status', 'active');
        })->pluck('user_id')->map(static fn ($id): int => (int) $id)->all();

        return array_values(array_unique(array_filter([...($owner === null ? [] : [(int) $owner]), ...$members])));
    }
}
