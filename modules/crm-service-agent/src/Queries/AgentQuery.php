<?php

declare(strict_types=1);

namespace Liberu\CRM\ServiceAgent\Queries;

use Liberu\CRM\ServiceAgent\Models\AgentCase;
use Liberu\CRM\ServiceAgent\Models\AgentKnowledge;
use Liberu\CRM\ServiceAgent\Models\AgentReview;
use Liberu\CRM\ServiceAgent\Models\AgentToolRun;

final class AgentQuery
{
    public function cases(int $teamId)
    {
        return AgentCase::query()->where('team_id', $teamId)->latest();
    }

    public function knowledge(int $teamId, string $search = '')
    {
        return AgentKnowledge::query()->where('team_id', $teamId)->where('active', true)->when($search !== '', fn ($q) => $q->where(fn ($q) => $q->where('title', 'like', '%'.$search.'%')->orWhere('content', 'like', '%'.$search.'%')))->latest();
    }

    public function tools(int $teamId)
    {
        return AgentToolRun::query()->where('team_id', $teamId)->latest();
    }

    public function reviews(int $teamId)
    {
        return AgentReview::query()->where('team_id', $teamId)->latest();
    }
}
