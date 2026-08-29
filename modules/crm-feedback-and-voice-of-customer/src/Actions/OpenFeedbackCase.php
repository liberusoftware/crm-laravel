<?php

declare(strict_types=1);

namespace Liberu\CRM\FeedbackAndVoiceOfCustomer\Actions;

use Liberu\CRM\FeedbackAndVoiceOfCustomer\Models\FeedbackCase;
use Liberu\CRM\FeedbackAndVoiceOfCustomer\Models\FeedbackResponse;
use Liberu\CRM\FeedbackAndVoiceOfCustomer\Services\FeedbackPolicy;

final class OpenFeedbackCase
{
    public function __construct(private readonly FeedbackPolicy $policy) {}

    public function execute(int $teamId, int $userId, FeedbackResponse $response, ?int $ownerId = null): FeedbackCase
    {
        abort_unless($response->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);

        return FeedbackCase::query()->create(['team_id' => $teamId, 'response_id' => $response->id, 'owner_id' => $ownerId, 'status' => 'open']);
    }
}
