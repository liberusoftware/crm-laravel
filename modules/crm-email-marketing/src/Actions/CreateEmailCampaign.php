<?php

declare(strict_types=1);

namespace Liberu\CRM\EmailMarketing\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\EmailMarketing\Models\EmailCampaign;
use Liberu\CRM\EmailMarketing\Services\EmailMarketingPolicy;

final class CreateEmailCampaign
{
    public function __construct(private readonly EmailMarketingPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): EmailCampaign
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['name' => ['required', 'string', 'max:160'], 'content_type' => ['required', 'in:drag_and_drop,code'], 'subject' => ['required', 'string', 'max:255'], 'content' => ['required', 'string'], 'personalization' => ['nullable', 'array'], 'dynamic_content' => ['nullable', 'array'], 'deliverability' => ['nullable', 'array'], 'throttle_per_minute' => ['nullable', 'integer', 'min:1'], 'scheduled_at' => ['nullable', 'date']])->validate();

        return EmailCampaign::query()->create(['team_id' => $teamId, 'owner_id' => $userId, ...$data]);
    }
}
