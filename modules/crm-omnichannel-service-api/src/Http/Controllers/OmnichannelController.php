<?php

declare(strict_types=1);

namespace Liberu\CRM\OmnichannelServiceApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\CRM\OmnichannelService\Actions\AssignConversation;
use Liberu\CRM\OmnichannelService\Actions\RecordInteraction;
use Liberu\CRM\OmnichannelService\Actions\RecordWorkspaceEvent;
use Liberu\CRM\OmnichannelService\Actions\StartConversation;
use Liberu\CRM\OmnichannelService\Models\Conversation;
use Liberu\CRM\OmnichannelService\Queries\ConversationQuery;

final class OmnichannelController
{
    private function context(Request $r): array
    {
        return [(int) $r->user()->current_team_id, (int) $r->user()->id];
    }

    public function index(Request $r, ConversationQuery $q): JsonResponse
    {
        return response()->json($q->forTeam($this->context($r)[0])->paginate());
    }

    public function store(Request $r, StartConversation $a): JsonResponse
    {
        [$t,$u] = $this->context($r);

        return response()->json($a->execute($t, $u, $r->all()), 201);
    }

    public function interaction(Request $r, Conversation $c, RecordInteraction $a): JsonResponse
    {
        [$t,$u] = $this->context($r);

        return response()->json($a->execute($t, $u, $c, $r->all()), 201);
    }

    public function assign(Request $r, Conversation $c, AssignConversation $a): JsonResponse
    {
        [$t,$u] = $this->context($r);

        return response()->json($a->execute($t, $u, $c, $r->all()));
    }

    public function workspaceEvent(Request $r, Conversation $c, RecordWorkspaceEvent $a): JsonResponse
    {
        [$t,$u] = $this->context($r);

        return response()->json($a->execute($t, $u, $c, $r->all()), 201);
    }
}
