<?php

declare(strict_types=1);

namespace Liberu\CRM\ProductWorkspace\Actions;

use Illuminate\Support\Facades\Validator;
use Liberu\CRM\ProductWorkspace\Models\ProductSync;
use Liberu\CRM\ProductWorkspace\Services\ProductWorkspacePolicy;

final class RecordProductSync
{
    public function __construct(private readonly ProductWorkspacePolicy $policy) {}

    public function execute(int $teamId, int $userId, array $input): ProductSync
    {
        abort_unless($this->policy->canManage($teamId, $userId), 403);
        $data = Validator::make($input, ['provider' => ['required', 'in:billing,ecommerce'], 'resource' => ['required', 'string', 'max:120'], 'status' => ['nullable', 'in:queued,running,completed,failed'], 'error' => ['nullable', 'string']])->validate();

        return ProductSync::query()->create(['team_id' => $teamId, 'completed_at' => ($data['status'] ?? 'queued') === 'completed' ? now() : null, ...$data]);
    }
}
