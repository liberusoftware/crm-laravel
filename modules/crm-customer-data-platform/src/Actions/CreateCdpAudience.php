<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerDataPlatform\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\CustomerDataPlatform\Models\CdpAudience;
use Liberu\CRM\CustomerDataPlatform\Services\CdpPolicy;

final class CreateCdpAudience
{
    public function __construct(private readonly CdpPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): CdpAudience
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['name' => ['required', 'string', 'max:160'], 'definition' => ['required', 'array'], 'status' => ['nullable', 'in:draft,active,paused']])->validate();

        return CdpAudience::query()->create(['team_id' => $teamId, ...$data]);
    }
}
