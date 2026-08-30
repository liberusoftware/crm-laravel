<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

class TaskAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Task $task) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $dueDate = $this->task->getAttribute('due_date');

        return (new MailMessage())
            ->line('A CRM task has been assigned to you.')
            ->line('Task: '.$this->task->name)
            ->line('Due Date: '.($dueDate instanceof Carbon ? $dueDate->format('Y-m-d H:i') : 'Not set'))
            ->action('View Task', url('/tasks/'.$this->task->id));
    }

    public function toArray(object $notifiable): array
    {
        $dueDate = $this->task->getAttribute('due_date');

        return [
            'task_id' => $this->task->id,
            'task_name' => $this->task->name,
            'due_date' => $dueDate instanceof Carbon ? $dueDate->toDateTimeString() : null,
        ];
    }
}
