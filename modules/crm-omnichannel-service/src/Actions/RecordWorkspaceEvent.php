<?php

declare(strict_types=1);

namespace Liberu\CRM\OmnichannelService\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\OmnichannelService\Models\Conversation;
use Liberu\CRM\OmnichannelService\Models\WorkspaceEvent;
use Liberu\CRM\OmnichannelService\Services\OmnichannelPolicy;

final class RecordWorkspaceEvent
{
    public function __construct(private readonly OmnichannelPolicy $policy) {}

    public function execute(int $teamId, int $userId, Conversation $conversation, array $input): WorkspaceEvent
    {
        abort_unless($conversation->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['kind' => ['required', 'in:collision_lock,swarm_member,suggested_reply'], 'status' => ['required', 'in:active,accepted,released'], 'payload' => ['nullable', 'array'], 'expires_at' => ['nullable', 'date']])->validate();

        return WorkspaceEvent::query()->create(['team_id' => $teamId, 'conversation_id' => $conversation->id, 'user_id' => $userId, ...$data]);
    }
}
