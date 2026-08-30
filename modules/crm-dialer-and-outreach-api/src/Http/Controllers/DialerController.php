<?php

declare(strict_types=1);

namespace Liberu\CRM\DialerAndOutreachApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Liberu\CRM\DialerAndOutreach\Actions\CreateDialerList;
use Liberu\CRM\DialerAndOutreach\Actions\QueueDialerCall;
use Liberu\CRM\DialerAndOutreach\Actions\RecordCallOutcome;
use Liberu\CRM\DialerAndOutreach\Actions\RetryDialerCall;
use Liberu\CRM\DialerAndOutreach\Models\DialerCall;
use Liberu\CRM\DialerAndOutreach\Models\DialerList;
use Liberu\CRM\DialerAndOutreach\Queries\DialerQuery;

final class DialerController extends Controller
{
    public function __construct(private readonly DialerQuery $query) {}

    private function context(): array
    {
        $user = request()->user();

        return [(int) $user->current_team_id, (int) $user->id];
    }

    public function index(): JsonResponse
    {
        [$teamId] = $this->context();

        return response()->json($this->query->lists($teamId)->get());
    }

    public function store(CreateDialerList $action): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($action->execute($teamId, $userId, request()->all()), 201);
    }

    public function queue(DialerList $list, QueueDialerCall $action): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($action->execute($teamId, $userId, $list, request()->all()), 201);
    }

    public function outcome(DialerCall $call, RecordCallOutcome $action): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($action->execute($teamId, $userId, $call, request()->all()), 201);
    }

    public function retry(DialerCall $call, RetryDialerCall $action): JsonResponse
    {
        [$teamId] = $this->context();

        return response()->json($action->execute($teamId, $call));
    }
}
