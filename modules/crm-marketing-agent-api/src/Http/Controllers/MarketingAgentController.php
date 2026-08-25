<?php

declare(strict_types=1);

namespace Liberu\CRM\MarketingAgentApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\CRM\MarketingAgent\Actions\CreateAgentRequest;
use Liberu\CRM\MarketingAgent\Actions\CreateExperiment;
use Liberu\CRM\MarketingAgent\Actions\RecordAgentCheck;
use Liberu\CRM\MarketingAgent\Models\AgentRequest;
use Liberu\CRM\MarketingAgent\Queries\AgentQuery;

final class MarketingAgentController
{
    private function c(Request $r): array
    {
        return [(int) $r->user()->current_team_id, (int) $r->user()->id];
    }

    public function index(Request $r, AgentQuery $q): JsonResponse
    {
        return response()->json($q->forTeam($this->c($r)[0])->paginate());
    }

    public function store(Request $r, CreateAgentRequest $a): JsonResponse
    {
        [$t,$u] = $this->c($r);

        return response()->json($a->execute($t, $u, $r->all()), 201);
    }

    public function check(Request $r, AgentRequest $request, RecordAgentCheck $a): JsonResponse
    {
        [$t,$u] = $this->c($r);

        return response()->json($a->execute($t, $u, $request, $r->all()), 201);
    }

    public function experiment(Request $r, AgentRequest $request, CreateExperiment $a): JsonResponse
    {
        [$t,$u] = $this->c($r);

        return response()->json($a->execute($t, $u, $request, $r->all()), 201);
    }
}
