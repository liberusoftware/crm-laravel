<?php

declare(strict_types=1);

namespace Liberu\CRM\SandboxAndReleaseManagement\Actions;

use Illuminate\Validation\ValidationException;
use Liberu\CRM\SandboxAndReleaseManagement\Models\ReleaseSnapshot;
use Liberu\CRM\SandboxAndReleaseManagement\Services\ReleasePolicy;

final class CreateSnapshot
{
    public function execute(int $teamId, int $actorId, array $data): ReleaseSnapshot
    {
        if (! app(ReleasePolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }validator($data, ['name' => ['required', 'string', 'max:255'], 'environment' => ['required', 'in:sandbox,staging,production'], 'configuration' => ['required', 'array'], 'test_data_policy' => ['nullable', 'array']])->validate();
        $config = $data['configuration'];
        $checksum = hash('sha256', (string) json_encode($config, JSON_THROW_ON_ERROR));

        return ReleaseSnapshot::query()->create(['team_id' => $teamId, 'name' => $data['name'], 'environment' => $data['environment'], 'configuration' => $config, 'test_data_policy' => $data['test_data_policy'] ?? [], 'checksum' => $checksum, 'created_by' => $actorId]);
    }
}
