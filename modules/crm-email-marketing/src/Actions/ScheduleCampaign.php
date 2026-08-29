<?php

declare(strict_types=1);

namespace Liberu\CRM\EmailMarketing\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\EmailMarketing\Models\EmailCampaign;
use Liberu\CRM\EmailMarketing\Services\EmailMarketingPolicy;

final class ScheduleCampaign
{
    public function __construct(private readonly EmailMarketingPolicy $policy) {}

    public function execute(int $teamId, int $userId, EmailCampaign $campaign, array $input): EmailCampaign
    {
        abort_unless($campaign->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['scheduled_at' => ['required', 'date', 'after:now'], 'status' => ['nullable', 'in:scheduled,paused']])->validate();
        $campaign->update([...$data, 'status' => $data['status'] ?? 'scheduled']);

        return $campaign->refresh();
    }
}
