<?php

namespace Tests\Feature\Api;

use App\Models\Contact;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create();
        Sanctum::actingAs($user);
    }

    public function test_can_list_tasks(): void
    {
        $beforeCount = Task::count();
        Task::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/tasks');

        $response->assertStatus(200)
            ->assertJsonPath('total', $beforeCount + 3)
            ->assertJsonCount(3, 'data');
    }

    public function test_can_search_filter_sort_and_paginate_tasks(): void
    {
        Task::factory()->create(['name' => 'Alpha task', 'status' => 'pending']);
        Task::factory()->create(['name' => 'Beta task', 'status' => 'completed']);

        $this->getJson('/api/v1/tasks?search=Beta&status=completed&sort_by=name&sort_direction=asc&per_page=1')
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Beta task')
            ->assertJsonPath('per_page', 1);
    }

    public function test_can_create_task(): void
    {
        $taskData = [
            'name' => 'New Task',
            'description' => 'Task description',
            'due_date' => now()->addDay()->toDateString(),
            'status' => 'pending',
        ];

        $response = $this->postJson('/api/v1/tasks', $taskData);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'name' => 'New Task',
                'description' => 'Task description',
                'status' => 'pending',
            ]);
    }

    public function test_can_create_a_recurring_task(): void
    {
        $response = $this->postJson('/api/v1/tasks', [
            'name' => 'Weekly pipeline review',
            'due_date' => now()->addDay()->toDateString(),
            'recurrence' => 'weekly',
        ]);

        $response->assertCreated()
            ->assertJsonPath('recurrence', 'weekly');
    }

    public function test_store_rejects_a_contact_from_another_team(): void
    {
        $user = User::factory()->withPersonalTeam()->create();
        Sanctum::actingAs($user);
        $foreignUser = User::factory()->withPersonalTeam()->create();
        $foreignContact = Contact::factory()->create(['team_id' => $foreignUser->currentTeam->id]);

        $this->postJson('/api/v1/tasks', [
            'name' => 'Cross-team task',
            'contact_id' => $foreignContact->id,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['contact_id']);
    }

    public function test_store_rejects_a_past_due_date(): void
    {
        $this->postJson('/api/v1/tasks', [
            'name' => 'Past task',
            'due_date' => now()->subDay()->toDateString(),
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['due_date']);
    }

    public function test_can_show_task(): void
    {
        $task = Task::factory()->create();

        $response = $this->getJson("/api/v1/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJson($task->toArray());
    }

    public function test_can_update_task(): void
    {
        $task = Task::factory()->create();
        $updatedData = [
            'name' => 'Updated Task',
            'description' => 'Updated description',
            'due_date' => '2023-07-15',
            'status' => 'in_progress',
        ];

        $response = $this->putJson("/api/v1/tasks/{$task->id}", $updatedData);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'Updated Task',
                'description' => 'Updated description',
                'status' => 'in_progress',
            ]);
    }

    public function test_can_delete_task(): void
    {
        $task = Task::factory()->create();

        $response = $this->deleteJson("/api/v1/tasks/{$task->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}
