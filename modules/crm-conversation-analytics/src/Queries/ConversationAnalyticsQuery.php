<?php

declare(strict_types=1);

namespace Liberu\CRM\ConversationAnalytics\Queries;

use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\ConversationAnalytics\Models\ConversationAnalysis;

final class ConversationAnalyticsQuery
{
    public function analyses(int $teamId): Builder
    {
        return ConversationAnalysis::query()->where('team_id', $teamId)->latest('observed_on');
    }

    public function trends(int $teamId): array
    {
        return $this->analyses($teamId)->get()->groupBy(fn ($analysis): string => substr((string) $analysis->getAttribute('observed_on'), 0, 7))->map(fn ($rows): array => ['count' => $rows->count(), 'average_score' => round((float) $rows->avg(fn ($analysis): float => (float) (($analysis->getAttribute('scorecard')['average'] ?? 0))), 2)])->all();
    }
}
