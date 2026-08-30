<?php

declare(strict_types=1);

namespace Liberu\CRM\Telephony\Actions;

use Illuminate\Support\Facades\DB;
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
        }

        $data = validator($data, ['name' => ['required', 'string', 'max:255'], 'strategy' => ['nullable', 'in:ring_all,round_robin,least_calls'], 'max_wait_seconds' => ['nullable', 'integer', 'min:1'], 'members' => ['nullable', 'array'], 'members.*' => ['integer', 'distinct']])->validate();
        foreach ($data['members'] ?? [] as $member) {
            if (! app(TelephonyPolicy::class)->isTeamMember($teamId, (int) $member, [], true)) {
                throw ValidationException::withMessages(['members' => 'All queue members must belong to this team.']);
            }
        }

        $queue = DB::transaction(fn (): TelephonyQueue => TelephonyQueue::query()->create(['team_id' => $teamId, 'name' => $data['name'], 'strategy' => $data['strategy'] ?? 'ring_all', 'max_wait_seconds' => $data['max_wait_seconds'] ?? 300, 'members' => array_map('intval', $data['members'] ?? [])]));
        app(TelephonyAudit::class)->record($teamId, $actorId, 'queue_created', ['queue_id' => $queue->id]);

        return $queue;
    }
}
