<?php

declare(strict_types=1);

namespace Liberu\CRM\LoyaltyApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\CRM\Loyalty\Actions\CreateProgram;
use Liberu\CRM\Loyalty\Actions\EnrollMember;
use Liberu\CRM\Loyalty\Actions\RecordPoints;
use Liberu\CRM\Loyalty\Models\LoyaltyMember;
use Liberu\CRM\Loyalty\Models\LoyaltyProgram;
use Liberu\CRM\Loyalty\Queries\LoyaltyQuery;

final class LoyaltyController
{
    private function c(Request $r): array
    {
        return [(int) $r->user()->current_team_id, (int) $r->user()->id];
    }

    public function index(Request $r, LoyaltyQuery $q): JsonResponse
    {
        return response()->json($q->forTeam($this->c($r)[0])->paginate());
    }

    public function store(Request $r, CreateProgram $a): JsonResponse
    {
        [$t,$u] = $this->c($r);

        return response()->json($a->execute($t, $u, $r->all()), 201);
    }

    public function enroll(Request $r, LoyaltyProgram $program, EnrollMember $a): JsonResponse
    {
        [$t,$u] = $this->c($r);

        return response()->json($a->execute($t, $u, $program, $r->all()), 201);
    }

    public function points(Request $r, LoyaltyMember $member, RecordPoints $a): JsonResponse
    {
        [$t,$u] = $this->c($r);

        return response()->json($a->execute($t, $u, $member, $r->all()), 201);
    }

    public function statement(Request $r, LoyaltyMember $member, LoyaltyQuery $q): JsonResponse
    {
        abort_unless($member->team_id === (int) $r->user()->current_team_id, 404);

        return response()->json($q->statement($member->team_id, $member->id)->paginate());
    }
}
