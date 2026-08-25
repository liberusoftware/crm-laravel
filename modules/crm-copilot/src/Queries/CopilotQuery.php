<?php

declare(strict_types=1);

namespace Liberu\CRM\Copilot\Queries;

use Liberu\CRM\Copilot\Models\CopilotRequest;

final class CopilotQuery
{
    public function requests(int $teamId, int $userId)
    {
        return CopilotRequest::query()->where('team_id', $teamId)->where('user_id', $userId)->latest();
    }
}
