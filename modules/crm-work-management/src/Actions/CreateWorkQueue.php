<?php

declare(strict_types=1);

namespace Liberu\CRM\WorkManagement\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Liberu\CRM\WorkManagement\Models\WorkQueue;
use Liberu\CRM\WorkManagement\Services\WorkAudit;

final class CreateWorkQueue
{
    /** @param array<string, mixed> $attributes */
    public function execute(int $teamId, ?int $actorId, array $attributes): WorkQueue
    {
        $data = Validator::make($attributes, [
            'name' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:500'],
            'rules' => ['nullable', 'array'],
        ])->validate();

        return DB::transaction(function () use ($teamId, $actorId, $data): WorkQueue {
            $queue = WorkQueue::query()->create([...$data, 'team_id' => $teamId, 'actor_id' => $actorId]);
            app(WorkAudit::class)->record($queue, $actorId, 'work_queue.created', ['status' => $queue->status]);

            return $queue->refresh();
        });
    }
}
