<?php

declare(strict_types=1);

namespace Liberu\CRM\ConversationIntelligenceApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Liberu\CRM\ConversationIntelligence\Actions\AddEvidence;
use Liberu\CRM\ConversationIntelligence\Actions\AnalyzeConversation;
use Liberu\CRM\ConversationIntelligence\Actions\RecordConversation;
use Liberu\CRM\ConversationIntelligence\Models\Conversation;
use Liberu\CRM\ConversationIntelligence\Queries\ConversationQuery;

final class ConversationIntelligenceController extends Controller
{
    public function __construct(private readonly ConversationQuery $query) {}

    private function context(): array
    {
        $u = request()->user();

        return [(int) $u->current_team_id, (int) $u->id];
    }

    public function index(): JsonResponse
    {
        [$t] = $this->context();

        return response()->json($this->query->conversations($t)->get());
    }

    public function store(RecordConversation $a): JsonResponse
    {
        [$t,$u] = $this->context();

        return response()->json($a->execute($t, $u, request()->all()), 201);
    }

    public function analyze(Conversation $c, AnalyzeConversation $a): JsonResponse
    {
        [$t] = $this->context();

        return response()->json($a->execute($t, $c, (string) request('transcript'), (array) request('insights', [])));
    }

    public function evidence(Conversation $c, AddEvidence $a): JsonResponse
    {
        [$t] = $this->context();

        return response()->json($a->execute($t, $c, (string) request('kind'), (string) request('label'), (string) request('content'), (array) request('metadata', [])), 201);
    }

    public function search(): JsonResponse
    {
        [$t] = $this->context();

        return response()->json($this->query->evidence($t, (string) request('q'))->values());
    }
}
