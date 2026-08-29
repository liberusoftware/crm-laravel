<?php

declare(strict_types=1);

namespace Liberu\CRM\SlaAndEntitlements\Actions;

use Illuminate\Validation\ValidationException;
use Liberu\CRM\SlaAndEntitlements\Models\SlaContract;
use Liberu\CRM\SlaAndEntitlements\Models\SlaEntitlement;
use Liberu\CRM\SlaAndEntitlements\Services\SlaPolicy;

final class SetEntitlement
{
    public function execute(int $teamId, int $actorId, array $data): SlaEntitlement
    {
        if (! app(SlaPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        } validator($data, ['contract_id' => ['required', 'integer', 'exists:crm_sla_contracts,id'], 'name' => ['required', 'string', 'max:255'], 'priority' => ['required', 'in:low,normal,high,urgent'], 'response_minutes' => ['required', 'integer', 'min:1'], 'resolution_minutes' => ['required', 'integer', 'gt:response_minutes'], 'warning_minutes' => ['nullable', 'integer', 'min:0']])->validate();

        if (! SlaContract::query()->where('team_id', $teamId)->whereKey($data['contract_id'])->exists()) {
            throw ValidationException::withMessages(['contract_id' => 'Contract does not belong to this team.']);
        }

        return SlaEntitlement::query()->create(array_merge($data, ['team_id' => $teamId, 'warning_minutes' => $data['warning_minutes'] ?? 30, 'active' => true]));
    }
}
