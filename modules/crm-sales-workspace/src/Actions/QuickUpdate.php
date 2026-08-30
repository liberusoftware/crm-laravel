<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesWorkspace\Actions;

use Illuminate\Validation\ValidationException;
use Liberu\CRM\SalesWorkspace\Models\WorkspaceItem;
use Liberu\CRM\SalesWorkspace\Models\WorkspaceUpdate;
use Liberu\CRM\SalesWorkspace\Services\WorkspacePolicy;

final class QuickUpdate
{
    public function execute(int $teamId, int $actorId, int $id, array $data): WorkspaceUpdate
    {
        if (! app(WorkspacePolicy::class)->canManage($teamId, $actorId)) {
            throw ValidationException::withMessages(['authorization' => 'Not authorized.']);
        }validator($data, ['type' => ['required', 'in:note,status,next_action,priority'], 'body' => ['nullable', 'string'], 'payload' => ['nullable', 'array']])->validate();
        $item = WorkspaceItem::query()->where('team_id', $teamId)->findOrFail($id);
        if ($data['type'] === 'status') {
            $item->status = $data['payload']['status'] ?? 'open';
        }if ($data['type'] === 'next_action') {
            $item->next_action = $data['body'] ?? null;
        }if ($data['type'] === 'priority') {
            $item->priority = $data['payload']['priority'] ?? 'normal';
        }$item->last_activity_at = now();
        $item->save();

        return WorkspaceUpdate::query()->create(['team_id' => $teamId, 'item_id' => $id, 'actor_id' => $actorId, 'type' => $data['type'], 'body' => $data['body'] ?? null, 'payload' => $data['payload'] ?? []]);
    }
}
