<?php

declare(strict_types=1);

namespace Liberu\CRM\ConversationAnalytics\Actions;

use Liberu\CRM\ConversationAnalytics\Models\ConversationAnalysis;

final class ScoreConversation
{
    /** @param array<string,int|float> $scores */
    public function execute(int $teamId, ConversationAnalysis $analysis, array $scores): ConversationAnalysis
    {
        abort_unless((int) $analysis->team_id === $teamId && $scores !== [], 422);
        $values = array_map('floatval', $scores);
        $analysis->update(['scorecard' => ['scores' => $scores, 'average' => round(array_sum($values) / count($values), 2), 'scored_at' => now()->toIso8601String()]]);

        return $analysis->refresh();
    }
}
