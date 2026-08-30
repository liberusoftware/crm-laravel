<?php

declare(strict_types=1);

namespace Liberu\CRM\FeedbackAndVoiceOfCustomer\Actions;

use Illuminate\Support\Str;
use Liberu\CRM\FeedbackAndVoiceOfCustomer\Models\FeedbackDelivery;
use Liberu\CRM\FeedbackAndVoiceOfCustomer\Models\FeedbackSurvey;
use Liberu\CRM\FeedbackAndVoiceOfCustomer\Services\FeedbackPolicy;

final class DeliverFeedbackSurvey
{
    public function __construct(private readonly FeedbackPolicy $policy) {}

    public function execute(int $teamId, int $userId, FeedbackSurvey $survey, ?int $recipientId, string $channel = 'email'): FeedbackDelivery
    {
        abort_unless($survey->team_id === $teamId && $this->policy->canManage($teamId, $userId), 403);
        abort_unless($survey->status === 'published', 422);

        return FeedbackDelivery::query()->create(['team_id' => $teamId, 'survey_id' => $survey->id, 'recipient_id' => $recipientId, 'channel' => $channel, 'status' => 'sent', 'token' => (string) Str::uuid(), 'sent_at' => now()]);
    }
}
