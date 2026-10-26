<?php

declare(strict_types=1);

namespace Liberu\CRM\CopilotApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Liberu\CRM\Copilot\Actions\AskCopilot;
use Liberu\CRM\Copilot\Actions\ConfirmCopilotAction;
use Liberu\CRM\Copilot\Actions\ProposeCopilotAction;
use Liberu\CRM\Copilot\Models\CopilotAction;
use Liberu\CRM\Copilot\Models\CopilotRequest;
use Liberu\CRM\Copilot\Queries\CopilotQuery;

final class CopilotController extends Controller
{
    public function __construct(private readonly CopilotQuery $query) {}

    private function context(): array
    {
        $user = request()->user();

        return [(int) $user->current_team_id, (int) $user->id];
    }

    public function ask(AskCopilot $action): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($action->execute($teamId, $userId, request()->all()), 201);
    }

    public function propose(CopilotRequest $request, ProposeCopilotAction $action): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($action->execute($teamId, $userId, $request, request()->all()), 201);
    }

    public function confirm(CopilotAction $action, ConfirmCopilotAction $confirm): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($confirm->execute($teamId, $userId, $action));
    }

    public function requests(): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($this->query->requests($teamId, $userId)->limit(25)->get());
    }
}
