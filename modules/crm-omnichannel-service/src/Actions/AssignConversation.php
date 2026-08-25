<?php

declare(strict_types=1);

namespace Liberu\CRM\OmnichannelService\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\OmnichannelService\Models\Conversation;
use Liberu\CRM\OmnichannelService\Services\OmnichannelPolicy;

final class AssignConversation
{
    public function __construct(private readonly OmnichannelPolicy $policy) {}

    public function execute(int $teamId, int $userId, Conversation $conversation, array $input): Conversation
    {
        abort_unless($conversation->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['assigned_to' => ['nullable', 'integer'], 'status' => ['nullable', 'in:open,pending,resolved,closed'], 'priority' => ['nullable', 'in:low,normal,high,urgent']])->validate();
        $conversation->fill($data)->save();

        return $conversation->refresh();
    }
}
