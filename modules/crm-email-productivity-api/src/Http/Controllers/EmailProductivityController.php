<?php

declare(strict_types=1);

namespace Liberu\CRM\EmailProductivityApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Liberu\CRM\EmailProductivity\Actions\ConnectMailbox;
use Liberu\CRM\EmailProductivity\Actions\CreateEmailTemplate;
use Liberu\CRM\EmailProductivity\Actions\RecordEmailEvent;
use Liberu\CRM\EmailProductivity\Actions\SendEmail;
use Liberu\CRM\EmailProductivity\Models\EmailMessage;
use Liberu\CRM\EmailProductivity\Queries\EmailProductivityQuery;

final class EmailProductivityController extends Controller
{
    public function __construct(private readonly EmailProductivityQuery $query) {}

    private function context(): array
    {
        $user = request()->user();

        return [(int) $user->current_team_id, (int) $user->id];
    }

    public function mailboxes(): JsonResponse
    {
        [$teamId] = $this->context();

        return response()->json($this->query->mailboxes($teamId)->get());
    }

    public function templates(): JsonResponse
    {
        [$teamId] = $this->context();

        return response()->json($this->query->templates($teamId)->get());
    }

    public function messages(): JsonResponse
    {
        [$teamId] = $this->context();

        return response()->json($this->query->messages($teamId)->get());
    }

    public function connect(ConnectMailbox $action): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($action->execute($teamId, $userId, request()->all()), 201);
    }

    public function template(CreateEmailTemplate $action): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($action->execute($teamId, $userId, request()->all()), 201);
    }

    public function send(SendEmail $action): JsonResponse
    {
        [$teamId,$userId] = $this->context();

        return response()->json($action->execute($teamId, $userId, request()->all()), 201);
    }

    public function event(EmailMessage $message, RecordEmailEvent $action): JsonResponse
    {
        [$teamId] = $this->context();

        return response()->json($action->execute($teamId, $message, request()->all()), 201);
    }
}
