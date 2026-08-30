<?php

declare(strict_types=1);

namespace Liberu\CRM\SlaAndEntitlements\Actions;

use Illuminate\Validation\ValidationException;
use Liberu\CRM\SlaAndEntitlements\Models\SlaCalendar;
use Liberu\CRM\SlaAndEntitlements\Models\SlaContract;
use Liberu\CRM\SlaAndEntitlements\Services\SlaPolicy;

final class CreateContract
{
    public function execute(int $teamId, int $actorId, array $data): SlaContract
    {
        if (! app(SlaPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }
        $data = validator($data, ['name' => ['required', 'string', 'max:255'], 'status' => ['nullable', 'in:draft,active,suspended,expired,terminated'], 'customer_id' => ['nullable', 'integer'], 'calendar_id' => ['nullable', 'integer', 'exists:crm_sla_calendars,id'], 'starts_on' => ['nullable', 'date'], 'ends_on' => ['nullable', 'date', 'after_or_equal:starts_on'], 'metadata' => ['nullable', 'array']])->validate();

        if (isset($data['calendar_id']) && ! SlaCalendar::query()->where('team_id', $teamId)->whereKey($data['calendar_id'])->exists()) {
            throw ValidationException::withMessages(['calendar_id' => 'Calendar does not belong to this team.']);
        }

        return SlaContract::query()->create(array_merge($data, ['team_id' => $teamId, 'status' => $data['status'] ?? 'draft']));
    }
}
