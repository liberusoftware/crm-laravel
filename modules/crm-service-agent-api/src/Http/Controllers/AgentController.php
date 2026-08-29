<?php

declare(strict_types=1);

namespace Liberu\CRM\ServiceAgentApi\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Liberu\CRM\ServiceAgent\Actions\ClassifyCase;
use Liberu\CRM\ServiceAgent\Actions\CreateAgentCase;
use Liberu\CRM\ServiceAgent\Actions\EscalateAgentCase;
use Liberu\CRM\ServiceAgent\Actions\RetrieveKnowledge;
use Liberu\CRM\ServiceAgent\Actions\ReviewAgentCase;
use Liberu\CRM\ServiceAgent\Actions\RunAgentTool;
use Liberu\CRM\ServiceAgent\Actions\UpdateAgentOutput;
use Liberu\CRM\ServiceAgent\Queries\AgentQuery;

final class AgentController extends Controller
{
    private function team(Request $r): int
    {
        abort_unless($r->user()?->current_team_id !== null, 403);

        return (int) $r->user()->current_team_id;
    }

    public function cases(Request $r, AgentQuery $q)
    {
        return response()->json(['data' => $q->cases($this->team($r))->paginate((int) $r->integer('per_page', 25))]);
    }

    public function storeCase(Request $r, CreateAgentCase $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function classify(Request $r, int $case, ClassifyCase $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $case, $r->all())]);
    }

    public function knowledge(Request $r, RetrieveKnowledge $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())]);
    }

    public function output(Request $r, int $case, string $type, UpdateAgentOutput $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $case, $type, $r->all())]);
    }

    public function tool(Request $r, RunAgentTool $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 202);
    }

    public function escalate(Request $r, int $case, EscalateAgentCase $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $case, $r->all())]);
    }

    public function review(Request $r, ReviewAgentCase $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }
}
