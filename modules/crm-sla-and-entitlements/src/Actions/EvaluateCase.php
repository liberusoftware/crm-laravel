<?php

declare(strict_types=1);

namespace Liberu\CRM\SlaAndEntitlements\Actions;

use Illuminate\Validation\ValidationException;
use Liberu\CRM\SlaAndEntitlements\Models\SlaCase;
use Liberu\CRM\SlaAndEntitlements\Models\SlaEntitlement;
use Liberu\CRM\SlaAndEntitlements\Models\SlaEscalation;
use Liberu\CRM\SlaAndEntitlements\Services\SlaPolicy;

final class EvaluateCase
{
    public function execute(int $teamId, int $actorId, int $caseId): array
    {
        if (! app(SlaPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        } $case = SlaCase::query()->where('team_id', $teamId)->findOrFail($caseId);
        $entitlement = $case->entitlement_id === null ? null : SlaEntitlement::query()->where('team_id', $teamId)->find($case->entitlement_id);
        $now = now();
        $warningMinutes = $entitlement === null ? 30 : $entitlement->warning_minutes;
        $warning = $case->resolution_due_at !== null && $case->resolved_at === null && $now->gte($case->resolution_due_at->copy()->subMinutes($warningMinutes));
        $breached = $case->resolution_due_at !== null && $case->resolved_at === null && $now->gt($case->resolution_due_at);
        $escalation = null;
        if ($breached) {
            $escalation = SlaEscalation::query()->firstOrCreate(['team_id' => $teamId, 'case_id' => $case->id, 'level' => 1], ['status' => 'triggered', 'triggered_at' => $now]);
        }

        return ['warning' => $warning, 'breached' => $breached, 'escalation' => $escalation];
    }
}
