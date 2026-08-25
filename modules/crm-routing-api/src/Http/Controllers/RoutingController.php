<?php

declare(strict_types=1);

namespace Liberu\CRM\RoutingApi\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Liberu\CRM\Routing\Actions\AcceptAssignment;
use Liberu\CRM\Routing\Actions\AssignSubject;
use Liberu\CRM\Routing\Actions\CreateRoutingRule;
use Liberu\CRM\Routing\Actions\UpsertRoutingAgent;
use Liberu\CRM\Routing\Queries\RoutingQuery;

final class RoutingController extends Controller
{
    private function team(Request $r): int
    {
        abort_unless($r->user()?->current_team_id !== null, 403);

        return (int) $r->user()->current_team_id;
    }

    public function rules(Request $r, RoutingQuery $q)
    {
        return response()->json(['data' => $q->rules($this->team($r))->get()]);
    }

    public function rule(Request $r, CreateRoutingRule $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function agent(Request $r, UpsertRoutingAgent $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())]);
    }

    public function agents(Request $r, RoutingQuery $q)
    {
        return response()->json(['data' => $q->agents($this->team($r))->get()]);
    }

    public function assignments(Request $r, RoutingQuery $q)
    {
        return response()->json(['data' => $q->assignments($this->team($r))->paginate((int) $r->integer('per_page', 25))]);
    }

    public function assign(Request $r, AssignSubject $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $r->all())], 201);
    }

    public function status(Request $r, int $assignment, string $status, AcceptAssignment $a)
    {
        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $assignment, $status)]);
    }
}
