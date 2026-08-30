<?php

declare(strict_types=1);

namespace Liberu\CRM\ConversationAnalyticsApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Liberu\CRM\ConversationAnalytics\Actions\RecordConversationAnalysis;
use Liberu\CRM\ConversationAnalytics\Actions\ScoreConversation;
use Liberu\CRM\ConversationAnalytics\Models\ConversationAnalysis;
use Liberu\CRM\ConversationAnalytics\Queries\ConversationAnalyticsQuery;

final class ConversationAnalyticsController extends Controller
{
    public function __construct(private readonly ConversationAnalyticsQuery $query) {}

    private function context(): array
    {
        $u = request()->user();

        return [(int) $u->current_team_id, (int) $u->id];
    }

    public function index(): JsonResponse
    {
        [$t] = $this->context();

        return response()->json($this->query->analyses($t)->get());
    }

    public function store(string $conversationKey, RecordConversationAnalysis $a): JsonResponse
    {
        [$t,$u] = $this->context();

        return response()->json($a->execute($t, $u, $conversationKey, request()->all()), 201);
    }

    public function score(ConversationAnalysis $analysis, ScoreConversation $a): JsonResponse
    {
        [$t] = $this->context();

        return response()->json($a->execute($t, $analysis, (array) request('scores', [])));
    }

    public function trends(): JsonResponse
    {
        [$t] = $this->context();

        return response()->json($this->query->trends($t));
    }
}
