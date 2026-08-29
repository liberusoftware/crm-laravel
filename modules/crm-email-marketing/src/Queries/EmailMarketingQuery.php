<?php

declare(strict_types=1);

namespace Liberu\CRM\EmailMarketing\Queries;

use Liberu\CRM\EmailMarketing\Models\EmailCampaign;
use Liberu\CRM\EmailMarketing\Models\EmailMarketingEvent;

final class EmailMarketingQuery
{
    public function campaigns(int $teamId)
    {
        return EmailCampaign::query()->where('team_id', $teamId)->latest();
    }

    public function analytics(int $teamId, int $campaignId): array
    {
        $query = EmailMarketingEvent::query()->where('team_id', $teamId)->where('campaign_id', $campaignId);

        return ['sent' => (clone $query)->where('event', 'sent')->count(), 'delivered' => (clone $query)->where('event', 'delivered')->count(), 'opened' => (clone $query)->where('event', 'opened')->count(), 'clicked' => (clone $query)->where('event', 'clicked')->count(), 'bounced' => (clone $query)->where('event', 'bounced')->count()];
    }
}
