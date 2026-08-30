<?php

declare(strict_types=1);

namespace Liberu\CRM\AIReceptionAndConversation\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\AIReceptionAndConversation\Models\ReceptionAgent;

final class CreateReceptionAgent
{
    /** @param array<string,mixed> $input */
    public function execute(int $teamId, int $ownerId, array $input): ReceptionAgent
    {
        $data = Validator::make($input, ['name' => ['required', 'string', 'max:180'], 'channel' => ['required', 'in:chat,voice'], 'knowledge' => ['nullable', 'array'], 'tools' => ['nullable', 'array'], 'policy' => ['nullable', 'array'], 'requires_human_approval' => ['nullable', 'boolean']])->validate();

        return ReceptionAgent::query()->create(array_merge($data, ['team_id' => $teamId, 'owner_id' => $ownerId, 'status' => 'draft', 'requires_human_approval' => $data['requires_human_approval'] ?? true]));
    }
}
