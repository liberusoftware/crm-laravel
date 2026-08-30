<?php

declare(strict_types=1);

namespace Liberu\CRM\Segmentation\Queries;

use Liberu\CRM\Segmentation\Models\Audience;
use Liberu\CRM\Segmentation\Models\AudienceMember;
use Liberu\CRM\Segmentation\Models\BehaviorEvent;

final class SegmentationQuery
{
    public function audiences(int $teamId)
    {
        return Audience::query()->where('team_id', $teamId)->latest();
    }

    public function members(int $teamId, int $audienceId)
    {
        return AudienceMember::query()->where('team_id', $teamId)->where('audience_id', $audienceId)->latest();
    }

    public function events(int $teamId)
    {
        return BehaviorEvent::query()->where('team_id', $teamId)->latest();
    }
}
