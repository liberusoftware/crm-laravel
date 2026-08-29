<?php

declare(strict_types=1);

namespace Liberu\CRM\DialerAndOutreach\Queries;

use Liberu\CRM\DialerAndOutreach\Models\DialerCall;
use Liberu\CRM\DialerAndOutreach\Models\DialerList;

final class DialerQuery
{
    public function lists(int $teamId)
    {
        return DialerList::query()->where('team_id', $teamId)->latest();
    }

    public function calls(int $teamId, int $listId)
    {
        return DialerCall::query()->where('team_id', $teamId)->where('list_id', $listId)->latest();
    }
}
