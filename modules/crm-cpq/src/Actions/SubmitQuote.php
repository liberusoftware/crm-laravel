<?php

declare(strict_types=1);

namespace Liberu\CRM\CPQ\Actions;

use Liberu\CRM\CPQ\Models\CpqApproval;
use Liberu\CRM\CPQ\Models\CpqQuote;

final class SubmitQuote
{
    public function execute(int $teamId, int $userId, CpqQuote $quote): CpqApproval
    {
        abort_unless($quote->team_id === $teamId && $quote->status === 'draft', 422);
        $quote->update(['status' => 'pending_approval']);

        return CpqApproval::query()->create(['team_id' => $teamId, 'quote_id' => $quote->id, 'actor_id' => $userId, 'status' => 'pending']);
    }
}
