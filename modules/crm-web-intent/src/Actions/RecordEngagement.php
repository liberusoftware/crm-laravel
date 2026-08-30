<?php

declare(strict_types=1);

namespace Liberu\CRM\WebIntent\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\WebIntent\Events\EngagementRecorded;
use Liberu\CRM\WebIntent\Models\WebIntentEngagement;
use Liberu\CRM\WebIntent\Models\WebIntentSignal;
use Liberu\CRM\WebIntent\Models\WebIntentVisit;
use Liberu\CRM\WebIntent\Services\WebIntentScorer;

final class RecordEngagement
{
    /** @param array<string, mixed> $attributes */
    public function execute(WebIntentVisit $visit, array $attributes, WebIntentScorer $scorer): WebIntentEngagement
    {
        if (($attributes['team_id'] ?? $visit->team_id) !== $visit->team_id) {
            throw ValidationException::withMessages(['visit' => 'The visit does not belong to this team.']);
        }
        $data = validator($attributes, ['event_type' => ['required', 'string', 'max:64'], 'page_url' => ['nullable', 'url', 'max:2048'], 'content_type' => ['nullable', 'string', 'max:100'], 'content_id' => ['nullable', 'string', 'max:190'], 'duration_seconds' => ['nullable', 'integer', 'min:0', 'max:86400'], 'dedupe_key' => ['nullable', 'string', 'max:190'], 'metadata' => ['nullable', 'array']])->validate();
        if (isset($data['dedupe_key'])) {
            $existing = WebIntentEngagement::query()->where('team_id', $visit->team_id)->where('dedupe_key', $data['dedupe_key'])->first();
            if ($existing instanceof WebIntentEngagement) {
                return $existing;
            }
        }

        return DB::transaction(function () use ($visit, $data, $scorer): WebIntentEngagement {
            $points = $scorer->points((string) $data['event_type'], isset($data['duration_seconds']) ? (int) $data['duration_seconds'] : null);
            $engagement = WebIntentEngagement::query()->create(array_merge($data, ['team_id' => $visit->team_id, 'visit_id' => $visit->getKey(), 'visitor_key' => $visit->visitor_key, 'points' => $points, 'occurred_at' => now()]));
            $lockedVisit = WebIntentVisit::query()->whereKey($visit->getKey())->lockForUpdate()->firstOrFail();
            $lockedVisit->score = min(100, $lockedVisit->score + $points);
            $lockedVisit->intent_level = $scorer->level($lockedVisit->score);
            $lockedVisit->save();
            WebIntentSignal::query()->create(['team_id' => $lockedVisit->team_id, 'visit_id' => $lockedVisit->getKey(), 'visitor_key' => $lockedVisit->visitor_key, 'signal' => (string) $data['event_type'], 'points' => $points, 'metadata' => $data['metadata'] ?? null, 'occurred_at' => now()]);

            DB::afterCommit(fn (): bool => event(new EngagementRecorded($engagement->fresh())) === null);

            return $engagement;
        });
    }
}
