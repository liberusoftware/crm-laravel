<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadCaptureApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\CRM\LeadCapture\Actions\CaptureLead;
use Liberu\CRM\LeadCapture\Actions\RecordCaptureEvent;
use Liberu\CRM\LeadCapture\Models\CapturedLead;
use Liberu\CRM\LeadCapture\Queries\CaptureQuery;

final class LeadCaptureController
{
    private function c(Request $r): array
    {
        return [(int) $r->user()->current_team_id, (int) $r->user()->id];
    }

    public function index(Request $r, CaptureQuery $q): JsonResponse
    {
        return response()->json($q->forTeam($this->c($r)[0])->paginate());
    }

    public function store(Request $r, CaptureLead $a): JsonResponse
    {
        [$t,$u] = $this->c($r);

        return response()->json($a->execute($t, $u, $r->all()), 201);
    }

    public function event(Request $r, CapturedLead $lead, RecordCaptureEvent $a): JsonResponse
    {
        [$t,$u] = $this->c($r);

        return response()->json($a->execute($t, $u, $lead, $r->all()), 201);
    }
}
