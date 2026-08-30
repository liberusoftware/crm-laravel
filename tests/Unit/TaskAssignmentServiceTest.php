<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskAssignedNotification;
use App\Services\TaskAssignmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TaskAssignmentServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_notifies_a_new_assignee(): void
    {
        Notification::fake();
        $assignee = User::factory()->create();
        $task = Task::factory()->create(['assigned_to' => $assignee->id]);

        app(TaskAssignmentService::class)->notify($task);

        Notification::assertSentTo($assignee, TaskAssignedNotification::class);
    }

    public function test_does_not_notify_when_the_assignee_is_unchanged(): void
    {
        Notification::fake();
        $assignee = User::factory()->create();
        $task = Task::factory()->create(['assigned_to' => $assignee->id]);

        app(TaskAssignmentService::class)->notify($task, $assignee->id);

        Notification::assertNothingSent();
    }
}
