<?php

declare(strict_types=1);

namespace Liberu\CRM\SlaAndEntitlements\Actions;

use Illuminate\Validation\ValidationException;
use Liberu\CRM\SlaAndEntitlements\Models\SlaCalendar;
use Liberu\CRM\SlaAndEntitlements\Services\SlaPolicy;

final class CreateCalendar
{
    public function execute(int $teamId, int $actorId, array $data): SlaCalendar
    {
        $this->authorize($teamId, $actorId);
        $data = validator($data, ['name' => ['required', 'string', 'max:255'], 'timezone' => ['required', 'timezone'], 'weekly_schedule' => ['nullable', 'array'], 'holidays' => ['nullable', 'array']])->validate();

        return SlaCalendar::query()->create(['team_id' => $teamId, 'name' => $data['name'], 'timezone' => $data['timezone'], 'weekly_schedule' => $data['weekly_schedule'] ?? [], 'holidays' => $data['holidays'] ?? [], 'active' => true]);
    }

    private function authorize(int $teamId, int $actorId): void
    {
        if (! app(SlaPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }
    }
}
