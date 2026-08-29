<?php

declare(strict_types=1);

namespace Liberu\CRM\ChatAndBotsApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
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

    private function context(): array
    {
        $u = request()->user();

        return [(int) $u->current_team_id, (int) $u->id];
    }

    public function bots(): JsonResponse
    {
        [$t] = $this->context();

        return response()->json($this->query->bots($t)->get());
    }

    public function store(CreateChatBot $a): JsonResponse
    {
        [$t,$u] = $this->context();

        return response()->json($a->execute($t, $u, (string) request('name'), (array) request('playbook', []), (array) request('channels', [])), 201);
    }

    public function session(ChatBot $bot, StartChatSession $a): JsonResponse
    {
        [$t] = $this->context();

        return response()->json($a->execute($t, $bot, (string) request('visitor_key'), (string) request('channel', 'web')), 201);
    }

    public function handoff(ChatSession $session, HandoffChatSession $a): JsonResponse
    {
        [$t] = $this->context();

        return response()->json($a->execute($t, $session, (string) request('assignee')));
    }
}
