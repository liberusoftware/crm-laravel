<?php

declare(strict_types=1);

namespace Liberu\CRM\FeedbackAndVoiceOfCustomer\Queries;

use Liberu\CRM\FeedbackAndVoiceOfCustomer\Models\FeedbackResponse;
use Liberu\CRM\FeedbackAndVoiceOfCustomer\Models\FeedbackSurvey;

final class FeedbackQuery
{
    public function surveys(int $teamId)
    {
        return FeedbackSurvey::query()->where('team_id', $teamId)->latest();
    }

    public function responses(int $teamId, int $surveyId)
    {
        return FeedbackResponse::query()->where('team_id', $teamId)->where('survey_id', $surveyId)->latest();
    }

    public function trend(int $teamId, int $surveyId): array
    {
        $query = FeedbackResponse::query()->where('team_id', $teamId)->where('survey_id', $surveyId);

        return ['count' => $query->count(), 'average_score' => round((float) $query->avg('score'), 2), 'positive' => $query->where('sentiment', 'positive')->count(), 'negative' => $query->where('sentiment', 'negative')->count()];
    }
}
