<?php

declare(strict_types=1);

namespace Liberu\CRM\JourneyOrchestrationApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\CRM\JourneyOrchestration\Actions\CreateJourney;
use Liberu\CRM\JourneyOrchestration\Actions\PublishJourney;
use Liberu\CRM\JourneyOrchestration\Actions\StartJourneyRun;
use Liberu\CRM\JourneyOrchestration\Actions\StopJourneyRun;
use Liberu\CRM\JourneyOrchestration\Models\Journey;
use Liberu\CRM\JourneyOrchestration\Models\JourneyRun;
use Liberu\CRM\JourneyOrchestration\Queries\JourneyQuery;

final class JourneyController
{
    private function c(Request $r): array
    {
        return [(int) $r->user()->current_team_id, (int) $r->user()->id];
    }

    public function index(Request $r, JourneyQuery $q): JsonResponse
    {
        return response()->json($q->forTeam($this->c($r)[0])->paginate());
    }

    public function store(Request $r, CreateJourney $a): JsonResponse
    {
        [$t,$u] = $this->c($r);

        return response()->json($a->execute($t, $u, $r->all()), 201);
    }

    public function publish(Request $r, Journey $journey, PublishJourney $a): JsonResponse
    {
        [$t,$u] = $this->c($r);

        return response()->json($a->execute($t, $u, $journey, $r->all()));
    }

    public function run(Request $r, Journey $journey, StartJourneyRun $a): JsonResponse
    {
        [$t,$u] = $this->c($r);

        return response()->json($a->execute($t, $u, $journey, $r->all()), 201);
    }

    public function stop(Request $r, JourneyRun $run, StopJourneyRun $a): JsonResponse
    {
        [$t,$u] = $this->c($r);

        return response()->json($a->execute($t, $u, $run, $r->all()), 201);
    }
}
