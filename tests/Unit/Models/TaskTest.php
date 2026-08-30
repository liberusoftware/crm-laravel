<?php

namespace Tests\Unit\Models;

use App\Models\Contact;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_task_can_be_created(): void
    {
        $user = User::factory()->withPersonalTeam()->create();
        $task = Task::factory()->create(['team_id' => $user->currentTeam->id]);

        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    }

    public function test_task_belongs_to_contact(): void
    {
        $user = User::factory()->withPersonalTeam()->create();
        $contact = Contact::factory()->create(['team_id' => $user->currentTeam->id]);
        $task = Task::factory()->create([
            'team_id' => $user->currentTeam->id,
            'contact_id' => $contact->id,
        ]);

        $this->assertInstanceOf(Contact::class, $task->contact);
    }

    public function test_task_due_date_is_cast_to_datetime(): void
    {
        $user = User::factory()->withPersonalTeam()->create();
        $task = Task::factory()->create([
            'team_id' => $user->currentTeam->id,
            'due_date' => '2025-06-01 10:00:00',
        ]);

        $this->assertInstanceOf(Carbon::class, $task->due_date);
    }

    public function test_task_reminder_sent_is_cast_to_boolean(): void
    {
        $user = User::factory()->withPersonalTeam()->create();
        $task = Task::factory()->create([
            'team_id' => $user->currentTeam->id,
            'reminder_sent' => false,
        ]);

        $this->assertIsBool($task->reminder_sent);
    }

    public function test_task_fillable_attributes(): void
    {
        $task = new Task();
        $this->assertContains('name', $task->getFillable());
        $this->assertContains('status', $task->getFillable());
        $this->assertContains('due_date', $task->getFillable());
    }

    public function test_completing_a_task_sets_completed_at(): void
    {
        $task = Task::factory()->create(['status' => 'pending']);

        $task->markAsComplete();

        $this->assertSame('completed', $task->fresh()->status);
        $this->assertNotNull($task->fresh()->completed_at);
    }

    public function test_reopening_a_completed_task_returns_it_to_pending(): void
    {
        $task = Task::factory()->create(['status' => 'completed']);

        $task->markAsIncomplete();

        $task = $task->fresh();
        $this->assertSame('pending', $task->status);
        $this->assertNull($task->completed_at);
    }

    public function test_completing_a_recurring_task_creates_the_next_occurrence_with_a_fresh_reminder(): void
    {
        $dueDate = now()->addDay()->startOfSecond();
        $reminderDate = $dueDate->copy()->subHour();
        $task = Task::factory()->create([
            'due_date' => $dueDate,
            'reminder_date' => $reminderDate,
            'reminder_sent' => true,
            'recurrence' => 'weekly',
            'status' => 'pending',
        ]);

        $task->markAsComplete();

        $nextTask = Task::query()
            ->where('id', '!=', $task->id)
            ->where('name', $task->name)
            ->firstOrFail();

        $this->assertSame('pending', $nextTask->status);
        $this->assertSame('weekly', $nextTask->recurrence);
        $this->assertTrue($nextTask->due_date->equalTo($dueDate->copy()->addWeek()));
        $this->assertTrue($nextTask->reminder_date->equalTo($reminderDate->copy()->addWeek()));
        $this->assertFalse($nextTask->reminder_sent);
        $this->assertSame($task->assigned_to, $nextTask->assigned_to);
    }

    public function test_directly_completing_a_recurring_task_also_creates_the_next_occurrence(): void
    {
        $task = Task::factory()->create([
            'due_date' => now()->addDay(),
            'recurrence' => 'daily',
            'status' => 'pending',
        ]);

        $task->update(['status' => 'completed']);

        $this->assertDatabaseHas('tasks', [
            'name' => $task->name,
            'status' => 'pending',
            'recurrence' => 'daily',
        ]);
        $this->assertSame(2, Task::query()->where('name', $task->name)->count());
    }
}
