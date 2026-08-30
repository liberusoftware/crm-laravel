<?php

declare(strict_types=1);

namespace Liberu\CRM\ConsentAndPreferences\Services;

use Illuminate\Support\Carbon;
use Liberu\CRM\ConsentAndPreferences\Models\ConsentRecord;
use Liberu\CRM\ConsentAndPreferences\Models\PolicyEvaluation;
use Liberu\CRM\ConsentAndPreferences\Models\PreferenceRecord;
use Liberu\CRM\ConsentAndPreferences\Models\SuppressionRecord;

final class PolicyEvaluator
{
    /** @return array{allowed: bool, reasons: list<string>, evaluation: PolicyEvaluation} */
    public function evaluate(int $teamId, string $subjectType, int $subjectId, string $channel, string $topic = 'general', ?Carbon $at = null): array
    {
        $at ??= now();
        $reasons = [];
        $consent = ConsentRecord::query()->where(['team_id' => $teamId, 'subject_type' => $subjectType, 'subject_id' => $subjectId, 'channel' => $channel, 'topic' => $topic])->latest('consented_at')->first();
        if (! $consent instanceof ConsentRecord || ! $consent->isActive()) {
            $reasons[] = 'missing_or_inactive_consent';
        }
        if ($consent?->expires_at?->isPast()) {
            $reasons[] = 'consent_expired';
        }
        $preference = PreferenceRecord::query()->where(['team_id' => $teamId, 'subject_type' => $subjectType, 'subject_id' => $subjectId, 'channel' => $channel, 'topic' => $topic])->first();
        if ($preference?->state === 'denied') {
            $reasons[] = 'preference_denied';
        }
        $suppressed = SuppressionRecord::query()->where('team_id', $teamId)->where('subject_type', $subjectType)->where('subject_id', $subjectId)->where(fn ($query) => $query->whereNull('channel')->orWhere('channel', $channel))->where(fn ($query) => $query->whereNull('topic')->orWhere('topic', $topic))->get()->contains(fn (SuppressionRecord $record): bool => $record->isActive());
        if ($suppressed) {
            $reasons[] = 'suppressed';
        }
        $allowed = $reasons === [];
        $evaluation = PolicyEvaluation::query()->create(['team_id' => $teamId, 'subject_type' => $subjectType, 'subject_id' => $subjectId, 'channel' => $channel, 'topic' => $topic, 'allowed' => $allowed, 'reasons' => $reasons, 'evaluated_at' => $at]);

        return ['allowed' => $allowed, 'reasons' => $reasons, 'evaluation' => $evaluation];
    }
}
