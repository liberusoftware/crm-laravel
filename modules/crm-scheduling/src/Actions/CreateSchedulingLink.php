<?php

declare(strict_types=1);

namespace Liberu\CRM\Scheduling\Actions;

use Illuminate\Validation\ValidationException;
use Liberu\CRM\Scheduling\Models\SchedulingLink;
use Liberu\CRM\Scheduling\Services\SchedulingPolicy;

final class CreateSchedulingLink
{
    public function execute(int $teamId, int $actorId, array $data): SchedulingLink
    {
        if (! app(SchedulingPolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }validator($data, ['slug' => ['required', 'alpha_dash', 'max:100'], 'name' => ['required', 'string', 'max:255'], 'kind' => ['required', 'in:personal,team,round_robin'], 'duration_minutes' => ['required', 'integer', 'min:5'], 'buffer_before' => ['nullable', 'integer', 'min:0'], 'buffer_after' => ['nullable', 'integer', 'min:0'], 'minimum_notice_minutes' => ['nullable', 'integer', 'min:0'], 'availability' => ['nullable', 'array'], 'questions' => ['nullable', 'array'], 'reminders' => ['nullable', 'array'], 'routing' => ['nullable', 'array'], 'calendar_adapter' => ['nullable', 'string', 'max:100']])->validate();

        return SchedulingLink::query()->create(array_merge($data, ['team_id' => $teamId, 'active' => true]));
    }
}
