<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskAssignedNotification;
use Illuminate\Support\Facades\Log;
use Throwable;

class TaskAssignmentService
{
    public function notify(Task $task, ?int $previousAssigneeId = null): void
    {
        if (! $task->assigned_to || $task->assigned_to === $previousAssigneeId) {
            return;
        }

        try {
            $task->loadMissing('assignedTo');
            $assignee = $task->assignedTo;
            if ($assignee instanceof User) {
                $assignee->notify(new TaskAssignedNotification($task));
            }
        } catch (Throwable $exception) {
            Log::error('Failed to notify task assignee.', [
                'task_id' => $task->id,
                'assigned_to' => $task->assigned_to,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
