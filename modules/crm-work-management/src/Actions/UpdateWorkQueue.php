<?php

declare(strict_types=1);

namespace Liberu\CRM\WorkManagement\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Liberu\CRM\WorkManagement\Models\WorkQueue;
use Liberu\CRM\WorkManagement\Services\WorkAudit;

final class UpdateWorkQueue
{
    /** @param array<string, mixed> $attributes */
    public function execute(WorkQueue $queue, ?int $actorId, array $attributes): WorkQueue
    {
        $data = Validator::make($attributes, [
            'name' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'in:active,paused,archived'],
            'rules' => ['nullable', 'array'],
        ])->validate();

        return DB::transaction(function () use ($queue, $actorId, $data): WorkQueue {
            $queue->update($data);
            app(WorkAudit::class)->record($queue, $actorId, 'work_queue.updated', ['fields' => array_keys($data)]);

            return $queue->refresh();
        });
    }
}
