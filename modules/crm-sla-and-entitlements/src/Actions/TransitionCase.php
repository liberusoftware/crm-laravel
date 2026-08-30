<?php

declare(strict_types=1);

namespace Liberu\CRM\SlaAndEntitlements\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\SlaAndEntitlements\Models\SlaCase;
use Liberu\CRM\SlaAndEntitlements\Services\SlaAudit;
use Liberu\CRM\SlaAndEntitlements\Services\SlaPolicy;

final class TransitionCase
{
    public function execute(int $teamId, int $actorId, int $caseId, string $transition, array $data = []): SlaCase
    {
        if (! app(SlaPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }

        return DB::transaction(function () use ($teamId, $actorId, $caseId, $transition, $data): SlaCase {
            $case = SlaCase::query()->where('team_id', $teamId)->lockForUpdate()->findOrFail($caseId);
            $allowed = ['responded' => 'responded', 'pause' => 'paused', 'resume' => 'open', 'resolve' => 'resolved', 'close' => 'closed'];
            if (! isset($allowed[$transition]) || ($transition === 'responded' && $case->responded_at !== null) || ($transition === 'resolve' && $case->resolved_at !== null)) {
                throw ValidationException::withMessages(['transition' => 'Invalid case transition.']);
            }
            $case->status = $allowed[$transition];
            if ($transition === 'responded') {
                $case->responded_at = now();
            }
            if ($transition === 'resolve') {
                $case->resolved_at = now();
            }
            if ($transition === 'pause') {
                $case->metadata = array_merge($case->metadata ?? [], ['pause_started_at' => now()->toIso8601String()]);
            }
            if ($transition === 'resume' && isset($case->metadata['pause_started_at'])) {
                $case->paused_minutes += (int) now()->diffInMinutes($case->metadata['pause_started_at']);
                $case->metadata = array_diff_key($case->metadata, ['pause_started_at' => true]);
            }
            $case->save();
            app(SlaAudit::class)->record($teamId, $actorId, $case->id, 'case_'.$transition, $data);

            return $case;
        });
    }
}
