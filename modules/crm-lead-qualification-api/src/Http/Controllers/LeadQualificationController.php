<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadQualificationApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\CRM\LeadQualification\Actions\RecordQualificationEvent;
use Liberu\CRM\LeadQualification\Actions\ScoreLead;
use Liberu\CRM\LeadQualification\Actions\UpsertLead;
use Liberu\CRM\LeadQualification\Models\QualifiedLead;
use Liberu\CRM\LeadQualification\Queries\LeadQualificationQuery;

final class LeadQualificationController
{
    private function c(Request $r): array
    {
        return [(int) $r->user()->current_team_id, (int) $r->user()->id];
    }

    public function index(Request $r, LeadQualificationQuery $q): JsonResponse
    {
        return response()->json($q->forTeam($this->c($r)[0])->paginate());
    }

    public function store(Request $r, UpsertLead $a): JsonResponse
    {
        [$t,$u] = $this->c($r);

        return response()->json($a->execute($t, $u, $r->all()), 201);
    }

    public function score(Request $r, QualifiedLead $lead, ScoreLead $a): JsonResponse
    {
        [$t,$u] = $this->c($r);

        return response()->json($a->execute($t, $u, $lead, $r->all()));
    }

    public function event(Request $r, QualifiedLead $lead, RecordQualificationEvent $a): JsonResponse
    {
        [$t,$u] = $this->c($r);

        return response()->json($a->execute($t, $u, $lead, $r->all()), 201);
    }
}
