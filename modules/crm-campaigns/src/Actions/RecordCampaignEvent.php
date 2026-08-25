<?php

declare(strict_types=1);

namespace Liberu\CRM\Campaigns\Actions;

use Liberu\CRM\Campaigns\Models\Campaign;
use Liberu\CRM\Campaigns\Models\CampaignEvent;

final class RecordCampaignEvent
{
    public function execute(int $teamId, int $actorId, Campaign $campaign, string $type, float $value = 0, array $payload = []): CampaignEvent
    {
        abort_unless((int) $campaign->team_id === $teamId && in_array($type, ['cost', 'response', 'influence', 'revenue', 'task_completed'], true), 422);
        $field = ['cost' => 'cost', 'influence' => 'influence', 'revenue' => 'revenue', 'response' => null, 'task_completed' => null][$type];
        if ($field !== null) {
            $campaign->increment($field, $value);
        }

        return CampaignEvent::query()->create(['team_id' => $teamId, 'campaign_id' => $campaign->id, 'actor_id' => $actorId, 'type' => $type, 'value' => $value, 'payload' => $payload]);
    }
}
