<?php

declare(strict_types=1);

namespace Liberu\CRM\DialerAndOutreach\Actions;

use Liberu\CRM\DialerAndOutreach\Models\DialerCall;

final class RetryDialerCall
{
    public function execute(int $teamId, DialerCall $call, int $maxAttempts = 3): DialerCall
    {
        abort_unless($call->team_id === $teamId, 403);
        abort_if($call->attempts >= $maxAttempts, 422, 'Retry limit reached.');
        abort_unless(in_array($call->status, ['completed', 'failed'], true) && in_array($call->outcome, ['voicemail', 'no_answer', 'busy', 'failed', 'canceled'], true), 422, 'This call cannot be retried.');
        $call->update(['status' => 'queued', 'provider_sid' => null, 'completed_at' => null, 'next_attempt_at' => now()->addMinutes(5)]);

        return $call->refresh();
    }
}
