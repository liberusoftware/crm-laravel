<?php

declare(strict_types=1);

namespace Liberu\CRM\Contracts\Actions;

use Liberu\CRM\Contracts\Models\Contract;
use Liberu\CRM\Contracts\Models\ContractEvent;

final class TransitionContract
{
    public function execute(int $teamId, int $userId, Contract $contract, string $type, string $status = 'completed', array $payload = []): ContractEvent
    {
        $allowed = ['submit' => 'pending_approval', 'approve' => 'approved', 'reject' => 'draft', 'sign' => 'active', 'amend' => 'amended', 'renew' => 'renewed', 'notice' => 'notice_sent'];
        abort_unless((int) $contract->team_id === $teamId && isset($allowed[$type]) && in_array($contract->status, ['draft', 'pending_approval', 'approved', 'active', 'amended', 'renewed'], true), 422);
        $next = $allowed[$type];
        $contract->update(['status' => $next, 'version' => $type === 'amend' ? $contract->version + 1 : $contract->version]);

        return ContractEvent::query()->create(['team_id' => $teamId, 'contract_id' => $contract->id, 'actor_id' => $userId, 'type' => $type, 'status' => $status, 'payload' => $payload]);
    }
}
