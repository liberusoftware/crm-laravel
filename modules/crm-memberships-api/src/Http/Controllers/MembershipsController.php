<?php

declare(strict_types=1);

namespace Liberu\CRM\MembershipsApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\CRM\Memberships\Actions\ChangeGrantStatus;
use Liberu\CRM\Memberships\Actions\CreatePlan;
use Liberu\CRM\Memberships\Actions\GrantAccess;
use Liberu\CRM\Memberships\Models\MembershipGrant;
use Liberu\CRM\Memberships\Models\MembershipPlan;
use Liberu\CRM\Memberships\Queries\MembershipQuery;

final class MembershipsController
{
    private function c(Request $r): array
    {
        return [(int) $r->user()->current_team_id, (int) $r->user()->id];
    }

    public function index(Request $r, MembershipQuery $q): JsonResponse
    {
        return response()->json($q->forTeam($this->c($r)[0])->paginate());
    }

    public function store(Request $r, CreatePlan $a): JsonResponse
    {
        [$t,$u] = $this->c($r);

        return response()->json($a->execute($t, $u, $r->all()), 201);
    }

    public function grant(Request $r, MembershipPlan $plan, GrantAccess $a): JsonResponse
    {
        [$t,$u] = $this->c($r);

        return response()->json($a->execute($t, $u, $plan, $r->all()), 201);
    }

    public function status(Request $r, MembershipGrant $grant, ChangeGrantStatus $a): JsonResponse
    {
        [$t,$u] = $this->c($r);

        return response()->json($a->execute($t, $u, $grant, $r->all()));
    }
}
