<?php

declare(strict_types=1);

namespace Liberu\CRM\OmnichannelService\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\OmnichannelService\Models\Conversation;
use Liberu\CRM\OmnichannelService\Services\OmnichannelPolicy;

final class StartConversation
{
    public function __construct(private readonly OmnichannelPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): Conversation
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['external_key' => ['required', 'string', 'max:255'], 'channel' => ['required', 'in:email,chat,social,phone'], 'priority' => ['nullable', 'in:low,normal,high,urgent'], 'subject' => ['nullable', 'string', 'max:255'], 'metadata' => ['nullable', 'array']])->validate();

        return Conversation::query()->firstOrCreate(['team_id' => $teamId, 'external_key' => $data['external_key']], ['channel' => $data['channel'], 'priority' => $data['priority'] ?? 'normal', 'subject' => $data['subject'] ?? null, 'metadata' => $data['metadata'] ?? null]);
    }
}
