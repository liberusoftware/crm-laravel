<?php

declare(strict_types=1);

namespace Liberu\CRM\ContactCenter\Queries;

use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\ContactCenter\Models\ContactCenterAgent;
use Liberu\CRM\ContactCenter\Models\ContactCenterEvent;

final class ContactCenterQuery
{
    public function agents(int $teamId): Builder
    {
        return ContactCenterAgent::query()->where('team_id', $teamId);
    }

    public function supervisorView(int $teamId): array
    {
        return ['agents' => $this->agents($teamId)->get(), 'open_events' => ContactCenterEvent::query()->where('team_id', $teamId)->where('status', 'open')->count(), 'sla_breaches' => ContactCenterEvent::query()->where('team_id', $teamId)->whereColumn('wait_seconds', '>', 'sla_seconds')->count()];
    }
}
