<?php

declare(strict_types=1);

namespace Liberu\CRM\EventsAndWebinars\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\EventsAndWebinars\Models\CrmEvent;
use Liberu\CRM\EventsAndWebinars\Services\EventPolicy;

final class CreateEvent
{
    public function __construct(private readonly EventPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): CrmEvent
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['name' => ['required', 'string', 'max:180'], 'slug' => ['required', 'alpha_dash', 'max:80'], 'format' => ['required', 'in:physical,virtual,hybrid'], 'status' => ['nullable', 'in:draft,published,cancelled,completed'], 'capacity' => ['nullable', 'integer', 'min:1'], 'starts_at' => ['required', 'date'], 'ends_at' => ['required', 'date', 'after:starts_at'], 'location' => ['nullable', 'string', 'max:255'], 'recording_url' => ['nullable', 'url'], 'provider' => ['nullable', 'array'], 'settings' => ['nullable', 'array']])->validate();

        return CrmEvent::query()->create(['team_id' => $teamId, ...$data]);
    }
}
