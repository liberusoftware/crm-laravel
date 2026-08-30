<?php

declare(strict_types=1);

namespace Liberu\CRM\TerritoriesAndOwnershipApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\CRM\TerritoriesAndOwnership\Actions\AssignOwner;
use Liberu\CRM\TerritoriesAndOwnership\Actions\CreateCoverage;
use Liberu\CRM\TerritoriesAndOwnership\Actions\UpsertTerritoryRule;
use Liberu\CRM\TerritoriesAndOwnership\Queries\TerritoryQuery;

final class TerritoryController extends Controller
{
    private function team(Request $r): int
    {
        abort_unless($r->user()?->current_team_id !== null, 403);

        return (int) $r->user()->current_team_id;
    }

    public function index(Request $r, TerritoryQuery $q): JsonResponse
    {
        return response()->json($q->rules($this->team($r)));
    }

    public function history(Request $r, TerritoryQuery $q): JsonResponse
    {
        return response()->json($q->history($this->team($r)));
    }

    public function store(Request $r, UpsertTerritoryRule $a): JsonResponse
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->getAuthIdentifier(), $r->validate([
            'name' => ['required', 'string', 'max:255'],
            'book_of_business' => ['nullable', 'string', 'max:255'],
            'criteria' => ['nullable', 'array'],
            'members' => ['required', 'array'],
            'members.*' => ['integer', 'distinct'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'active' => ['sometimes', 'boolean'],
        ]))], 201);
    }

    public function assign(Request $r, AssignOwner $a): JsonResponse
    {
        $data = $r->validate(['subject_type' => ['required', 'string', 'max:100'], 'subject_id' => ['required', 'integer', 'min:1'], 'previous_owner_id' => ['nullable', 'integer', 'min:1'], 'owner_id' => ['required', 'integer', 'min:1'], 'reason' => ['required', 'string', 'max:255']]);

        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->getAuthIdentifier(), $data['subject_type'], $data['subject_id'], $data['previous_owner_id'] ?? null, $data['owner_id'], $data['reason'])], 201);
    }

    public function coverage(Request $r, CreateCoverage $a): JsonResponse
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->getAuthIdentifier(), $r->validate([
            'rule_id' => ['required', 'integer', 'min:1'],
            'covered_user_id' => ['required', 'integer', 'min:1'],
            'substitute_user_id' => ['required', 'integer', 'different:covered_user_id', 'min:1'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
        ]))], 201);
    }
}
