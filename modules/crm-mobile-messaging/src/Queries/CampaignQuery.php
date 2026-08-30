<?php

declare(strict_types=1);

namespace Liberu\CRM\MobileMessaging\Queries;

use Liberu\CRM\MobileMessaging\Models\MessagingCampaign;

final class CampaignQuery
{
    public function forTeam(int $teamId)
    {
        return MessagingCampaign::query()->where('team_id', $teamId)->with('messages')->latest();
    }
}
