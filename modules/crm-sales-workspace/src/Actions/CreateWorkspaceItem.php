<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesWorkspace\Actions;

use Illuminate\Validation\ValidationException;
use Liberu\CRM\SalesWorkspace\Models\WorkspaceItem;
use Liberu\CRM\SalesWorkspace\Services\WorkspacePolicy;

final class CreateWorkspaceItem
{
    public function execute(int $teamId, int $actorId, array $data): WorkspaceItem
    {
        if (! app(WorkspacePolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }validator($data, ['kind' => ['required', 'in:lead,deal,task,follow_up'], 'title' => ['required', 'string', 'max:255'], 'priority' => ['nullable', 'in:low,normal,high,urgent'], 'due_at' => ['nullable', 'date'], 'next_action' => ['nullable', 'string', 'max:255'], 'risk_indicators' => ['nullable', 'array'], 'customer_history' => ['nullable', 'array']])->validate();

        return WorkspaceItem::query()->create(array_merge($data, ['team_id' => $teamId, 'owner_id' => $data['owner_id'] ?? $actorId, 'status' => 'open', 'priority' => $data['priority'] ?? 'normal']));
    }
}
