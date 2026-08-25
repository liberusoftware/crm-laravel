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
        $call->increment('attempts');
        $call->update(['status' => 'queued', 'next_attempt_at' => now()->addMinutes(5)]);

        return $call->refresh();
    }
}
