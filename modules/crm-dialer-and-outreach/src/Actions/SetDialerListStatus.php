<?php

declare(strict_types=1);

namespace Liberu\CRM\DialerAndOutreach\Actions;

use Liberu\CRM\DialerAndOutreach\Models\DialerList;
use Liberu\CRM\DialerAndOutreach\Services\DialerPolicy;

final class SetDialerListStatus
{
    public function execute(int $teamId, int $userId, int $listId, string $status): void
    {
        abort_unless(app(DialerPolicy::class)->canManage($teamId, $userId), 403);
        abort_unless(in_array($status, ['active', 'paused'], true), 422);
        DialerList::query()->where('team_id', $teamId)->findOrFail($listId)->update(['status' => $status]);
    }
}
