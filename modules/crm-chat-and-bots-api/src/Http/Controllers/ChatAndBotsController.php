<?php

declare(strict_types=1);

namespace Liberu\CRM\ChatAndBotsApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\CRM\ChatAndBots\Actions\CreateChatBot;
use Liberu\CRM\ChatAndBots\Actions\HandoffChatSession;
use Liberu\CRM\ChatAndBots\Actions\StartChatSession;
use Liberu\CRM\ChatAndBots\Models\ChatBot;
use Liberu\CRM\ChatAndBots\Models\ChatSession;
use Liberu\CRM\ChatAndBots\Queries\ChatBotQuery;

final class ChatAndBotsController extends Controller
{
    public function __construct(private readonly ChatBotQuery $query) {}

    private function context(Request $request): array
    {
        $u = $request->user();
        abort_unless($u !== null, 401);
        $teamId = (int) $u->getAttribute('current_team_id');
        abort_unless($teamId > 0, 403);

        return [$teamId, (int) $u->getKey()];
    }

    public function bots(Request $request): JsonResponse
    {
        [$t] = $this->context($request);
        $bots = $this->query->bots($t)->paginate(min($request->integer('per_page', 25), 100));

        return response()->json(['data' => $bots->getCollection()->map(fn (ChatBot $bot): array => $bot->toArray())->values(), 'meta' => ['current_page' => $bots->currentPage(), 'last_page' => $bots->lastPage(), 'per_page' => $bots->perPage(), 'total' => $bots->total()]]);
    }

    public function store(Request $request, CreateChatBot $a): JsonResponse
    {
        [$t,$u] = $this->context($request);

        return response()->json(['data' => $a->execute($t, $u, (string) $request->input('name'), (array) $request->input('playbook', []), (array) $request->input('channels', []))->toArray()], 201);
    }

    public function session(Request $request, ChatBot $bot, StartChatSession $a): JsonResponse
    {
        [$t] = $this->context($request);

        return response()->json(['data' => $a->execute($t, $bot, (string) $request->input('visitor_key'), (string) $request->input('channel', 'web'))->toArray()], 201);
    }

    public function handoff(Request $request, ChatSession $session, HandoffChatSession $a): JsonResponse
    {
        [$t] = $this->context($request);

        return response()->json(['data' => $a->execute($t, $session, (string) $request->input('assignee'))->toArray()]);
    }
}
