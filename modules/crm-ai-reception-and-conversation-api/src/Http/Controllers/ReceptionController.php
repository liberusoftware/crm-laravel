<?php

declare(strict_types=1);

namespace Liberu\CRM\AIReceptionAndConversationApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Liberu\CRM\AIReceptionAndConversation\Actions\ActivateReceptionAgent;
use Liberu\CRM\AIReceptionAndConversation\Actions\CreateReceptionAgent;
use Liberu\CRM\AIReceptionAndConversation\Actions\RecordReceptionTurn;
use Liberu\CRM\AIReceptionAndConversation\Actions\RequestReceptionHandoff;
use Liberu\CRM\AIReceptionAndConversation\Actions\StartReceptionConversation;
use Liberu\CRM\AIReceptionAndConversation\Models\ReceptionAgent;
use Liberu\CRM\AIReceptionAndConversation\Models\ReceptionConversation;

final class ReceptionController extends Controller
{
    private function context(): array
    {
        $user = request()->user();

        return [(int) $user->current_team_id, (int) $user->id];
    }

    public function agents(): JsonResponse
    {
        [$teamId] = $this->context();

        return response()->json(ReceptionAgent::query()->where('team_id', $teamId)->latest()->get());
    }

    public function createAgent(CreateReceptionAgent $action): JsonResponse
    {
        [$teamId, $actorId] = $this->context();

        return response()->json($action->execute($teamId, $actorId, request()->all()), 201);
    }

    public function activate(ReceptionAgent $agent, ActivateReceptionAgent $action): JsonResponse
    {
        return response()->json($action->execute($this->context()[0], $agent));
    }

    public function start(ReceptionAgent $agent, StartReceptionConversation $action): JsonResponse
    {
        return response()->json($action->execute($this->context()[0], $agent, request()->all()), 201);
    }

    public function turn(ReceptionConversation $conversation, RecordReceptionTurn $action): JsonResponse
    {
        [$teamId, $actorId] = $this->context();

        return response()->json($action->execute($teamId, $actorId, $conversation, request()->all()));
    }

    public function handoff(ReceptionConversation $conversation, RequestReceptionHandoff $action): JsonResponse
    {
        [$teamId, $actorId] = $this->context();

        return response()->json($action->execute($teamId, $actorId, $conversation, (string) request('reason')));
    }
}
