<?php

declare(strict_types=1);

namespace Liberu\CRM\ConversationAnalytics\Actions;

use Liberu\CRM\ConversationAnalytics\Models\ConversationAnalysis;

final class RecordConversationAnalysis
{
    /** @param array<string,mixed> $input */
    public function execute(int $teamId, int $analystId, string $conversationKey, array $input): ConversationAnalysis
    {
        $conversationKey = trim($conversationKey);
        abort_unless($conversationKey !== '' && isset($input['observed_on']), 422);

        return ConversationAnalysis::query()->updateOrCreate(['team_id' => $teamId, 'conversation_key' => $conversationKey], ['analyst_id' => $analystId, 'topics' => $input['topics'] ?? [], 'objections' => $input['objections'] ?? [], 'competitors' => $input['competitors'] ?? [], 'questions' => $input['questions'] ?? [], 'outcomes' => $input['outcomes'] ?? [], 'talk_ratios' => $input['talk_ratios'] ?? [], 'coaching_moments' => $input['coaching_moments'] ?? [], 'scorecard' => $input['scorecard'] ?? [], 'observed_on' => $input['observed_on']]);
    }
}
