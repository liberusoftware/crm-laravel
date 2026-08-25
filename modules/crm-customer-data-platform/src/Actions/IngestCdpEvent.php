<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerDataPlatform\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\CustomerDataPlatform\Models\CdpEvent;
use Liberu\CRM\CustomerDataPlatform\Models\CdpProfile;

final class IngestCdpEvent
{
    public function execute(int $teamId, CdpProfile $profile, array $input): CdpEvent
    {
        abort_unless($profile->team_id === $teamId, 403);
        $data = Validator::make($input, ['name' => ['required', 'string', 'max:120'], 'payload' => ['nullable', 'array'], 'consented' => ['required', 'boolean'], 'occurred_at' => ['nullable', 'date']])->validate();
        abort_unless(($profile->consent['analytics'] ?? false) || ! ($data['consented'] ?? false), 422, 'Profile consent does not permit this event.');

        return CdpEvent::query()->create(['team_id' => $teamId, 'profile_id' => $profile->id, 'occurred_at' => $data['occurred_at'] ?? now(), ...$data]);
    }
}
