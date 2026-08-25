<?php

declare(strict_types=1);

namespace Liberu\CRM\SlaAndEntitlements\Actions;

use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\SlaAndEntitlements\Models\SlaCase;
use Liberu\CRM\SlaAndEntitlements\Models\SlaEntitlement;
use Liberu\CRM\SlaAndEntitlements\Services\SlaAudit;
use Liberu\CRM\SlaAndEntitlements\Services\SlaPolicy;

final class OpenCase
{
    public function execute(int $teamId, int $actorId, array $data): SlaCase
    {
        if (! app(SlaPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        } validator($data, ['subject' => ['required', 'string', 'max:255'], 'contract_id' => ['nullable', 'integer', 'exists:crm_sla_contracts,id'], 'entitlement_id' => ['nullable', 'integer', 'exists:crm_sla_entitlements,id'], 'opened_at' => ['nullable', 'date']])->validate();
        $opened = isset($data['opened_at']) ? Carbon::parse($data['opened_at']) : now();
        $entitlement = isset($data['entitlement_id']) ? SlaEntitlement::query()->where('team_id', $teamId)->find($data['entitlement_id']) : null;
        if (isset($data['entitlement_id']) && $entitlement === null) {
            throw ValidationException::withMessages(['entitlement_id' => 'Entitlement does not belong to this team.']);
        } $case = SlaCase::query()->create(['team_id' => $teamId, 'contract_id' => $data['contract_id'] ?? null, 'entitlement_id' => $entitlement?->id, 'subject' => $data['subject'], 'status' => 'open', 'opened_at' => $opened, 'response_due_at' => $entitlement?->response_minutes ? $opened->copy()->addMinutes($entitlement->response_minutes) : null, 'resolution_due_at' => $entitlement?->resolution_minutes ? $opened->copy()->addMinutes($entitlement->resolution_minutes) : null, 'metadata' => $data['metadata'] ?? []]);
        app(SlaAudit::class)->record($teamId, $actorId, $case->id, 'case_opened');

        return $case;
    }
}
