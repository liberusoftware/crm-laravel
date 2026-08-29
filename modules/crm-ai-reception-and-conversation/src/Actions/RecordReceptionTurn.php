<?php

declare(strict_types=1);

namespace Liberu\CRM\AIReceptionAndConversation\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\AIReceptionAndConversation\Models\ReceptionConversation;

final class RecordReceptionTurn
{
    /** @param array<string,mixed> $input */
    public function execute(int $teamId, int $actorId, ReceptionConversation $conversation, array $input): ReceptionConversation
    {
        abort_unless((int) $conversation->team_id === $teamId, 404);
        abort_unless($conversation->status === 'active', 422);
        $data = Validator::make($input, ['role' => ['required', 'in:user,assistant,tool'], 'content' => ['required', 'string'], 'confidence' => ['nullable', 'numeric', 'between:0,1'], 'qualification' => ['nullable', 'array'], 'booking' => ['nullable', 'array'], 'summary' => ['nullable', 'string']])->validate();
        $transcript = array_merge((array) $conversation->transcript, [['role' => $data['role'], 'content' => $data['content']]]);
        $conversation->update(['transcript' => $transcript, 'confidence' => $data['confidence'] ?? $conversation->confidence, 'qualification' => $data['qualification'] ?? $conversation->qualification, 'booking' => $data['booking'] ?? $conversation->booking, 'summary' => $data['summary'] ?? $conversation->summary]);
        $conversation->audits()->create(['team_id' => $teamId, 'actor_id' => $actorId, 'type' => 'turn_recorded', 'payload' => ['role' => $data['role'], 'confidence' => $data['confidence'] ?? null]]);

        return $conversation->fresh();
    }
}
