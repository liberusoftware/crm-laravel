<?php

declare(strict_types=1);

namespace Liberu\CRM\PlaybooksAndEnablement\Queries;

use Liberu\CRM\PlaybooksAndEnablement\Models\Playbook;
use Liberu\CRM\PlaybooksAndEnablement\Models\PlaybookAssignment;
use Liberu\CRM\PlaybooksAndEnablement\Models\PlaybookRecommendation;
use Liberu\CRM\PlaybooksAndEnablement\Models\PlaybookUsage;

final class PlaybookQuery
{
    public function playbooks(int $teamId)
    {
        return Playbook::query()->where('team_id', $teamId)->latest();
    }

    public function assignments(int $teamId)
    {
        return PlaybookAssignment::query()->where('team_id', $teamId)->latest();
    }

    public function recommendations(int $teamId)
    {
        return PlaybookRecommendation::query()->where('team_id', $teamId)->latest();
    }

    public function usage(int $teamId)
    {
        return PlaybookUsage::query()->where('team_id', $teamId)->latest();
    }
}
