<?php

declare(strict_types=1);

namespace Liberu\CRM\AIReceptionAndConversationApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
    private function context(Request $request): array
    {
        $user = $request->user();
        abort_unless($user !== null, 401);
        $teamId = (int) $user->getAttribute('current_team_id');
        abort_unless($teamId > 0, 403);

        return [$teamId, (int) $user->getKey()];
    }

    public function agents(Request $request): JsonResponse
    {
        [$teamId] = $this->context($request);
        $agents = ReceptionAgent::query()->where('team_id', $teamId)->latest()->paginate(min($request->integer('per_page', 25), 100));

        return response()->json(['data' => $agents->getCollection()->map(fn (ReceptionAgent $agent): array => $agent->toArray())->values(), 'meta' => ['current_page' => $agents->currentPage(), 'last_page' => $agents->lastPage(), 'per_page' => $agents->perPage(), 'total' => $agents->total()]]);
    }

    public function createAgent(Request $request, CreateReceptionAgent $action): JsonResponse
    {
        [$teamId, $actorId] = $this->context($request);

        return response()->json(['data' => $action->execute($teamId, $actorId, $request->all())->toArray()], 201);
    }

    public function activate(Request $request, ReceptionAgent $agent, ActivateReceptionAgent $action): JsonResponse
    {
        return response()->json(['data' => $action->execute($this->context($request)[0], $agent)->toArray()]);
    }

    public function start(Request $request, ReceptionAgent $agent, StartReceptionConversation $action): JsonResponse
    {
        return response()->json(['data' => $action->execute($this->context($request)[0], $agent, $request->all())->toArray()], 201);
    }

    public function turn(Request $request, ReceptionConversation $conversation, RecordReceptionTurn $action): JsonResponse
    {
        [$teamId, $actorId] = $this->context($request);

        return response()->json(['data' => $action->execute($teamId, $actorId, $conversation, $request->all())->toArray()]);
    }

    public function handoff(Request $request, ReceptionConversation $conversation, RequestReceptionHandoff $action): JsonResponse
    {
        [$teamId, $actorId] = $this->context($request);

        return response()->json(['data' => $action->execute($teamId, $actorId, $conversation, (string) $request->input('reason'))->toArray()]);
    }
}
