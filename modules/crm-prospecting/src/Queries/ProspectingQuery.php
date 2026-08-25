<?php

declare(strict_types=1);

namespace Liberu\CRM\Prospecting\Queries;

use Liberu\CRM\Prospecting\Models\IdealCustomerProfile;
use Liberu\CRM\Prospecting\Models\Prospect;
use Liberu\CRM\Prospecting\Models\ProspectExport;
use Liberu\CRM\Prospecting\Models\ProspectResearchItem;
use Liberu\CRM\Prospecting\Models\ProspectSearch;

final class ProspectingQuery
{
    public function profiles(int $teamId)
    {
        return IdealCustomerProfile::query()->where('team_id', $teamId)->latest();
    }

    public function searches(int $teamId)
    {
        return ProspectSearch::query()->where('team_id', $teamId)->latest();
    }

    public function prospects(int $teamId)
    {
        return Prospect::query()->where('team_id', $teamId)->latest();
    }

    public function queue(int $teamId)
    {
        return ProspectResearchItem::query()->where('team_id', $teamId)->latest();
    }

    public function exports(int $teamId)
    {
        return ProspectExport::query()->where('team_id', $teamId)->latest();
    }
}
