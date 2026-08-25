<?php

declare(strict_types=1);

namespace Liberu\CRM\FeedbackAndVoiceOfCustomer\Actions;

use Liberu\CRM\FeedbackAndVoiceOfCustomer\Models\FeedbackAlert;
use Liberu\CRM\FeedbackAndVoiceOfCustomer\Services\FeedbackPolicy;

final class ResolveFeedbackAlert
{
    public function __construct(private readonly FeedbackPolicy $policy) {}

    public function execute(int $teamId, int $userId, FeedbackAlert $alert): FeedbackAlert
    {
        abort_unless($alert->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);
        $alert->update(['status' => 'resolved', 'resolved_at' => now()]);

        return $alert->refresh();
    }
}
