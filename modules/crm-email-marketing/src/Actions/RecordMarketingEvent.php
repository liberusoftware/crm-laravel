<?php

declare(strict_types=1);

namespace Liberu\CRM\EmailMarketing\Actions;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\EmailMarketing\Models\EmailCampaign;
use Liberu\CRM\EmailMarketing\Models\EmailDelivery;
use Liberu\CRM\EmailMarketing\Models\EmailMarketingEvent;

final class RecordMarketingEvent
{
    public function execute(int $teamId, EmailCampaign $campaign, array $input): EmailMarketingEvent
    {
        abort_unless($campaign->team_id === $teamId, 403);
        $data = Validator::make($input, ['delivery_id' => ['nullable', 'integer', 'exists:crm_email_marketing_deliveries,id'], 'event' => ['required', 'in:sent,delivered,opened,clicked,bounced,unsubscribed'], 'metadata' => ['nullable', 'array']])->validate();
        if (isset($data['delivery_id']) && ! EmailDelivery::query()->where('team_id', $teamId)->where('campaign_id', $campaign->id)->whereKey($data['delivery_id'])->exists()) {
            throw ValidationException::withMessages(['delivery_id' => 'Delivery does not belong to this campaign.']);
        }

        return EmailMarketingEvent::query()->create(['team_id' => $teamId, 'campaign_id' => $campaign->id, 'occurred_at' => now(), ...$data]);
    }
}
