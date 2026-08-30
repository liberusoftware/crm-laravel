<?php

declare(strict_types=1);

namespace Liberu\CRM\OmnichannelService\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\OmnichannelService\Models\Conversation;
use Liberu\CRM\OmnichannelService\Models\Interaction;
use Liberu\CRM\OmnichannelService\Services\OmnichannelPolicy;

final class RecordInteraction
{
    public function __construct(private readonly OmnichannelPolicy $policy) {}

    public function execute(int $teamId, int $userId, Conversation $conversation, array $input): Interaction
    {
        abort_unless($conversation->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['direction' => ['required', 'in:inbound,outbound,internal'], 'body' => ['required', 'string', 'max:100000'], 'author_type' => ['nullable', 'string'], 'author_id' => ['nullable', 'integer'], 'external_key' => ['nullable', 'string'], 'metadata' => ['nullable', 'array'], 'occurred_at' => ['required', 'date']])->validate();

        return Interaction::query()->create(['team_id' => $teamId, 'conversation_id' => $conversation->id, ...$data]);
    }
}
