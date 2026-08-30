<?php

declare(strict_types=1);

namespace Liberu\CRM\WebIntent\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\WebIntent\Events\VisitRecorded;
use Liberu\CRM\WebIntent\Models\WebIntentVisit;
use Liberu\CRM\WebIntent\Services\WebIntentAudit;

final class RecordVisit
{
    /** @param array<string, mixed> $attributes */
    public function execute(int $teamId, string $visitorKey, array $attributes = []): WebIntentVisit
    {
        $data = validator(['visitor_key' => $visitorKey, ...$attributes], ['visitor_key' => ['required', 'string', 'max:128'], 'session_key' => ['nullable', 'string', 'max:128'], 'landing_url' => ['nullable', 'url', 'max:2048'], 'referrer' => ['nullable', 'url', 'max:2048'], 'consent_status' => ['nullable', 'in:unknown,granted,denied,withdrawn'], 'metadata' => ['nullable', 'array']])->validate();
        unset($data['visitor_key']);
        if (($data['consent_status'] ?? 'unknown') === 'denied') {
            throw ValidationException::withMessages(['consent_status' => 'A denied visitor cannot be tracked.']);
        }

        return DB::transaction(function () use ($teamId, $visitorKey, $data): WebIntentVisit {
            $visit = WebIntentVisit::query()->create(array_merge($data, ['team_id' => $teamId, 'visitor_key' => $visitorKey, 'started_at' => now(), 'intent_level' => 'unknown', 'status' => 'active']));
            DB::afterCommit(fn (): bool => event(new VisitRecorded($visit->fresh())) === null);
            app(WebIntentAudit::class)->record($teamId, null, 'visit_recorded', ['visitor_key' => $visitorKey]);

            return $visit;
        });
    }
}
