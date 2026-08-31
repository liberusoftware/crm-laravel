<?php

namespace App\Services;

use App\Models\Task;
use Illuminate\Support\Carbon;

class RecurringTaskService
{
    public function createNextOccurrence(Task $task): ?Task
    {
        $recurrence = $task->recurrence;
        if (! in_array($recurrence, ['daily', 'weekly', 'monthly'], true)) {
            return null;
        }

        $dueDate = $task->getAttribute('due_date');
        if (! $dueDate instanceof Carbon) {
            return null;
        }

        $nextDueDate = $dueDate->copy()->add(...match ($recurrence) {
            'daily' => [1, 'day'],
            'weekly' => [1, 'week'],
            'monthly' => [1, 'month'],
        });
        $reminderDate = $task->getAttribute('reminder_date');
        $nextReminderDate = $reminderDate instanceof Carbon
            ? $reminderDate->copy()->add(...match ($recurrence) {
                'daily' => [1, 'day'],
                'weekly' => [1, 'week'],
                'monthly' => [1, 'month'],
            })
            : null;

        return Task::create([
            'team_id' => $task->team_id,
            'name' => $task->name,
            'description' => $task->description,
            'due_date' => $nextDueDate,
            'recurrence' => $recurrence,
            'status' => 'pending',
            'contact_id' => $task->contact_id,
            'lead_id' => $task->lead_id,
            'company_id' => $task->company_id,
            'opportunity_id' => $task->opportunity_id,
            'reminder_date' => $nextReminderDate,
            'reminder_sent' => false,
            'google_event_id' => null,
            'outlook_event_id' => null,
            'calendar_type' => $task->calendar_type,
            'assigned_to' => $task->assigned_to,
            'overdue_notified' => false,
        ]);
    }
}
