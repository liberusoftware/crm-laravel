<?php

declare(strict_types=1);

namespace Liberu\CRM\MobileMessaging\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\MobileMessaging\Models\MessagingCampaign;
use Liberu\CRM\MobileMessaging\Models\MessagingMessage;
use Liberu\CRM\MobileMessaging\Services\MobileMessagingPolicy;

final class RecordMessage
{
    public function __construct(private readonly MobileMessagingPolicy $policy) {}

    public function execute(int $teamId, int $userId, MessagingCampaign $campaign, array $input): MessagingMessage
    {
        abort_unless($campaign->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['contact_id' => ['required', 'integer'], 'direction' => ['required', 'in:outbound,inbound'], 'status' => ['required', 'in:queued,sent,delivered,failed,received,opted_out'], 'body' => ['required', 'string', 'max:100000'], 'external_key' => ['nullable', 'string'], 'payload' => ['nullable', 'array'], 'sent_at' => ['nullable', 'date']])->validate();

        return MessagingMessage::query()->create(['team_id' => $teamId, 'campaign_id' => $campaign->id, ...$data]);
    }
}
