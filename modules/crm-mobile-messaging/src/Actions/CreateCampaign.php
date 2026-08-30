<?php

declare(strict_types=1);

namespace Liberu\CRM\MobileMessaging\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\MobileMessaging\Models\MessagingCampaign;
use Liberu\CRM\MobileMessaging\Services\MobileMessagingPolicy;

final class CreateCampaign
{
    public function __construct(private readonly MobileMessagingPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): MessagingCampaign
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['name' => ['required', 'string', 'max:255'], 'channel' => ['required', 'in:sms,mms,whatsapp,push'], 'template_id' => ['nullable', 'integer'], 'sender' => ['nullable', 'string', 'max:255'], 'quiet_start' => ['nullable', 'date'], 'quiet_end' => ['nullable', 'date'], 'metadata' => ['nullable', 'array']])->validate();

        return MessagingCampaign::query()->create(['team_id' => $teamId, ...$data]);
    }
}
