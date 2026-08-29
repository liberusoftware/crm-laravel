<?php

declare(strict_types=1);

namespace Liberu\CRM\ReputationManagement\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\ReputationManagement\Models\ReputationConnection;
use Liberu\CRM\ReputationManagement\Services\ReputationPolicy;

final class ConnectReviewSite
{
    public function __construct(private readonly ReputationPolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): ReputationConnection
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['site' => ['required', 'string', 'max:100'], 'location' => ['nullable', 'string', 'max:255'], 'credentials' => ['nullable', 'array'], 'metadata' => ['nullable', 'array']])->validate();

        return ReputationConnection::query()->updateOrCreate(['team_id' => $teamId, 'site' => $data['site'], 'location' => $data['location'] ?? null], ['team_id' => $teamId, ...$data, 'status' => 'active']);
    }
}
