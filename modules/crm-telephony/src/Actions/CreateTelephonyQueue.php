<?php

declare(strict_types=1);

namespace Liberu\CRM\Telephony\Actions;

use Illuminate\Validation\ValidationException;
use Liberu\CRM\Telephony\Models\TelephonyQueue;
use Liberu\CRM\Telephony\Services\TelephonyAudit;
use Liberu\CRM\Telephony\Services\TelephonyPolicy;

final class CreateTelephonyQueue
{
    public function execute(int $teamId, int $actorId, array $data): TelephonyQueue
    {
        if (! app(TelephonyPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        } validator($data, ['name' => ['required', 'string', 'max:255'], 'strategy' => ['nullable', 'in:ring_all,round_robin,least_calls'], 'max_wait_seconds' => ['nullable', 'integer', 'min:1'], 'members' => ['nullable', 'array']])->validate();
        $queue = TelephonyQueue::query()->create(['team_id' => $teamId, 'name' => $data['name'], 'strategy' => $data['strategy'] ?? 'ring_all', 'max_wait_seconds' => $data['max_wait_seconds'] ?? 300, 'members' => $data['members'] ?? []]);
        app(TelephonyAudit::class)->record($teamId, $actorId, 'queue_created', ['queue_id' => $queue->id]);

        return $queue;
    }
}
