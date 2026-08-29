<?php

declare(strict_types=1);

namespace Liberu\CRM\SandboxAndReleaseManagement\Actions;

use Illuminate\Validation\ValidationException;
use Liberu\CRM\SandboxAndReleaseManagement\Models\ReleaseChangeset;
use Liberu\CRM\SandboxAndReleaseManagement\Services\ReleasePolicy;

final class CreateChangeset
{
    public function execute(int $teamId, int $actorId, array $data): ReleaseChangeset
    {
        if (! app(ReleasePolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }validator($data, ['name' => ['required', 'string', 'max:255'], 'changes' => ['required', 'array'], 'dependencies' => ['nullable', 'array'], 'source_environment' => ['required', 'in:sandbox,staging,production'], 'target_environment' => ['required', 'in:sandbox,staging,production', 'different:source_environment']])->validate();

        return ReleaseChangeset::query()->create(array_merge($data, ['team_id' => $teamId, 'status' => 'draft', 'created_by' => $actorId]));
    }
}
