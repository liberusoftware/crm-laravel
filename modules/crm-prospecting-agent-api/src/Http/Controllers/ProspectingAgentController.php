<?php

declare(strict_types=1);

namespace Liberu\CRM\ProspectingAgentApi\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Liberu\CRM\ProspectingAgent\Actions\ApproveAgentRun;
use Liberu\CRM\ProspectingAgent\Actions\CreateAgentRun;
use Liberu\CRM\ProspectingAgent\Actions\DispatchSequence;
use Liberu\CRM\ProspectingAgent\Actions\PrepareSequence;
use Liberu\CRM\ProspectingAgent\Actions\RecordEngagement;
use Liberu\CRM\ProspectingAgent\Actions\SelectTarget;
use Liberu\CRM\ProspectingAgent\Queries\ProspectingAgentQuery;

final class ProspectingAgentController extends Controller
{
    private function team(Request $r): int
    {
        abort_unless($r->user()?->current_team_id !== null, 403);

        return (int) $r->user()->current_team_id;
    }

    public function runs(Request $r, ProspectingAgentQuery $q)
    {
        return response()->json(['data' => $q->runs($this->team($r))->get()]);
    }

    public function run(Request $r, CreateAgentRun $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function approve(Request $r, int $run, ApproveAgentRun $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $run)]);
    }

    public function target(Request $r, SelectTarget $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function sequence(Request $r, PrepareSequence $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function dispatch(Request $r, int $sequence, DispatchSequence $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $sequence)]);
    }

    public function engagement(Request $r, RecordEngagement $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }
}
