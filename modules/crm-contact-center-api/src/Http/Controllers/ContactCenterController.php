<?php

declare(strict_types=1);

namespace Liberu\CRM\ContactCenterApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Liberu\CRM\ContactCenter\Actions\RouteInteraction;
use Liberu\CRM\ContactCenter\Actions\SetAgentPresence;
use Liberu\CRM\ContactCenter\Queries\ContactCenterQuery;

final class ContactCenterController extends Controller
{
    public function __construct(private readonly ContactCenterQuery $query) {}

    private function context(): array
    {
        $u = request()->user();

        return [(int) $u->current_team_id, (int) $u->id];
    }

    public function agents(): JsonResponse
    {
        [$t] = $this->context();

        return response()->json($this->query->agents($t)->get());
    }

    public function presence(SetAgentPresence $a): JsonResponse
    {
        [$t,$u] = $this->context();

        return response()->json($a->execute($t, $u, (string) request('presence'), (int) request('capacity', 1), (array) request('skills', [])));
    }

    public function route(RouteInteraction $a): JsonResponse
    {
        [$t] = $this->context();

        return response()->json($a->execute($t, (string) request('queue_key'), (string) request('required_skill'), (int) request('sla_seconds', 300)), 201);
    }

    public function supervisor(): JsonResponse
    {
        [$t] = $this->context();

        return response()->json($this->query->supervisorView($t));
    }
}
