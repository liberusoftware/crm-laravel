<?php

declare(strict_types=1);

namespace Liberu\CRM\SlaAndEntitlements\Actions;

use Illuminate\Validation\ValidationException;
use Liberu\CRM\SlaAndEntitlements\Models\SlaCase;
use Liberu\CRM\SlaAndEntitlements\Models\SlaException;
use Liberu\CRM\SlaAndEntitlements\Services\SlaPolicy;

final class RequestException
{
    public function execute(int $teamId, int $actorId, array $data): SlaException
    {
        if (! app(SlaPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }
        $data = validator($data, ['case_id' => ['required', 'integer', 'exists:crm_sla_cases,id'], 'reason' => ['required', 'string', 'max:1000'], 'expires_at' => ['nullable', 'date', 'after:now']])->validate();
        if (! SlaCase::query()->where('team_id', $teamId)->whereKey($data['case_id'])->exists()) {
            throw ValidationException::withMessages(['case_id' => 'Case does not belong to this team.']);
        }

        return SlaException::query()->create(['team_id' => $teamId, 'case_id' => $data['case_id'], 'reason' => $data['reason'], 'status' => 'pending', 'requested_by' => $actorId, 'expires_at' => $data['expires_at'] ?? null]);
    }
}
