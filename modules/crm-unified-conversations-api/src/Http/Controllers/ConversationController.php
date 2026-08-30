<?php

declare(strict_types=1);

namespace Liberu\CRM\UnifiedConversationsApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\CRM\UnifiedConversations\Actions\OpenConversation;
use Liberu\CRM\UnifiedConversations\Actions\SendMessage;
use Liberu\CRM\UnifiedConversations\Queries\ConversationQuery;

final class ConversationController extends Controller
{
    private function team(Request $r): int
    {
        abort_unless($r->user()?->current_team_id !== null, 403);

        return (int) $r->user()->current_team_id;
    }

    public function index(Request $r, ConversationQuery $q): JsonResponse
    {
        return response()->json($q->list($this->team($r)));
    }

    public function store(Request $r, OpenConversation $a): JsonResponse
    {
        $data = $r->validate(['channel' => ['required', 'string', 'max:50'], 'external_id' => ['nullable', 'string', 'max:255'], 'subject' => ['nullable', 'string', 'max:255']]);

        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $data)], 201);
    }

    public function message(Request $r, int $conversation, SendMessage $a): JsonResponse
    {
        $data = $r->validate(['body' => ['required', 'string', 'max:10000'], 'internal' => ['boolean'], 'idempotency_key' => ['nullable', 'string', 'max:255']]);

        return response()->json(['data' => $a->execute($this->team($r), (int) $r->user()->id, $conversation, $data)], 201);
    }
}
