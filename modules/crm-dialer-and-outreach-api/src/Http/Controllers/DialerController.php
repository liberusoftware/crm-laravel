<?php

declare(strict_types=1);

namespace Liberu\CRM\DialerAndOutreachApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\CRM\DialerAndOutreach\Actions\CreateDialerList;
use Liberu\CRM\DialerAndOutreach\Actions\QueueDialerCall;
use Liberu\CRM\DialerAndOutreach\Actions\RecordCallOutcome;
use Liberu\CRM\DialerAndOutreach\Actions\RetryDialerCall;
use Liberu\CRM\DialerAndOutreach\Models\DialerCall;
use Liberu\CRM\DialerAndOutreach\Models\DialerList;
use Liberu\CRM\DialerAndOutreach\Queries\DialerQuery;
use Liberu\CRM\DialerAndOutreach\Services\DialerPolicy;

final class DialerController extends Controller
{
    public function __construct(private readonly DialerQuery $query) {}

    private function context(): array
    {
        $user = request()->user();

        return [(int) $user->getAttribute('current_team_id'), (int) $user->id];
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

    public function dispatch(DialerList $list, QueueDialerCall $action): JsonResponse
    {
        [$teamId, $userId] = $this->context();

        return response()->json($action->dispatch($teamId, $userId, $list));
    }

    public function status(DialerCall $call, Request $request): JsonResponse
    {
        abort_unless((int) $request->query('attempt') === (int) $call->attempts, 403);
        $data = $request->validate([
            'CallSid' => ['required', 'string', 'regex:/^CA[0-9a-fA-F]{32}$/'],
            'CallStatus' => ['required', 'in:completed,busy,no-answer,failed,canceled'],
        ]);
        abort_if($call->provider_sid !== null && $call->provider_sid !== $data['CallSid'], 403);
        DialerCall::query()->whereKey($call->id)->whereIn('status', ['dispatching', 'in_progress', 'dispatch_unknown'])->update([
            'provider_sid' => $data['CallSid'], 'status' => 'completed',
            'outcome' => match ($data['CallStatus']) {
                'no-answer' => 'no_answer', 'completed' => null, default => $data['CallStatus'],
            }, 'completed_at' => now(),
        ]);

        return response()->json(['received' => true]);
    }

    public function state(DialerList $list, Request $request, DialerPolicy $policy): JsonResponse
    {
        [$teamId, $userId] = $this->context();
        abort_unless((int) $list->team_id === $teamId && $policy->canManage($teamId, $userId), 403);
        $data = $request->validate(['status' => ['required', 'in:active,paused']]);
        $list->update($data);

        return response()->json($list->refresh());
    }
}
