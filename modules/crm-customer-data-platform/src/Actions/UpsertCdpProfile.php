<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerDataPlatform\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\CustomerDataPlatform\Models\CdpProfile;
use Liberu\CRM\CustomerDataPlatform\Services\CdpPolicy;

final class UpsertCdpProfile
{
    public function __construct(private readonly CdpPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): CdpProfile
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['profile_key' => ['required', 'string', 'max:160'], 'attributes' => ['nullable', 'array'], 'consent' => ['required', 'array']])->validate();

        return CdpProfile::query()->updateOrCreate(['team_id' => $teamId, 'profile_key' => $data['profile_key']], ['team_id' => $teamId, ...$data]);
    }
}
