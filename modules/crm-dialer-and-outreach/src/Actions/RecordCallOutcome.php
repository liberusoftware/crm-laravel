<?php

declare(strict_types=1);

namespace Liberu\CRM\DialerAndOutreach\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\DialerAndOutreach\Models\DialerCall;
use Liberu\CRM\DialerAndOutreach\Models\DialerEvent;

final class RecordCallOutcome
{
    public function execute(int $teamId, int $userId, DialerCall $call, array $input): DialerEvent
    {
        abort_unless($call->team_id === $teamId, 403);
        $data = Validator::make($input, ['outcome' => ['required', 'in:connected,voicemail,no_answer,busy,wrong_number,do_not_call'], 'detection' => ['nullable', 'in:human,machine,unknown'], 'voicemail_dropped' => ['nullable', 'boolean'], 'metadata' => ['nullable', 'array']])->validate();
        $call->update(['status' => 'completed', 'outcome' => $data['outcome'], 'detection' => $data['detection'] ?? null, 'voicemail_dropped' => $data['voicemail_dropped'] ?? false, 'completed_at' => now()]);

        return DialerEvent::query()->create(['team_id' => $teamId, 'call_id' => $call->id, 'actor_id' => $userId, 'event' => $data['outcome'], 'metadata' => $data['metadata'] ?? null, 'occurred_at' => now()]);
    }
}
