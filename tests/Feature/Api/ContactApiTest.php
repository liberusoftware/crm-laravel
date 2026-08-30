<?php

namespace Tests\Feature\Api;

use App\Models\Company;
use App\Models\Contact;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ContactApiTest extends TestCase
{
    use RefreshDatabase;

    private User $authenticatedUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authenticatedUser = User::factory()->create();
        Sanctum::actingAs($this->authenticatedUser);
    }

    public function test_can_list_contacts(): void
    {
        $beforeCount = Contact::count();
        Contact::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/contacts');

        $response->assertStatus(200)
            ->assertJsonPath('total', $beforeCount + 3)
            ->assertJsonCount(3, 'data');
    }

    public function test_can_search_filter_sort_and_paginate_contacts(): void
    {
        Contact::factory()->create(['name' => 'Alpha', 'status' => 'active']);
        Contact::factory()->create(['name' => 'Beta', 'status' => 'inactive']);
        Contact::factory()->create(['name' => 'Gamma', 'status' => 'active']);

        $response = $this->getJson('/api/v1/contacts?search=Gamma&status=active&sort_by=name&sort_direction=asc&per_page=1');

        $response->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Gamma')
            ->assertJsonPath('per_page', 1);
    }

    public function test_can_create_contact(): void
    {
        $contactData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone_number' => '1234567890',
        ];

        $response = $this->postJson('/api/v1/contacts', $contactData);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'John Doe', 'email' => 'john@example.com']);
    }

    public function test_store_rejects_a_company_from_another_team(): void
    {
        $foreignTeam = Team::factory()->create();
        $foreignCompany = Company::factory()->create(['team_id' => $foreignTeam->id]);

        $this->postJson('/api/v1/contacts', [
            'name' => 'Cross-team contact',
            'email' => 'cross-team@example.com',
            'company_id' => $foreignCompany->id,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['company_id']);
    }

    public function test_can_show_contact(): void
    {
        $contact = Contact::factory()->create();

        $response = $this->getJson("/api/v1/contacts/{$contact->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $contact->id,
                'name' => $contact->name,
                'email' => $contact->email,
            ]);
    }

    public function test_can_update_contact(): void
    {
        $contact = Contact::factory()->create();
        $updatedData = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ];

        $response = $this->putJson("/api/v1/contacts/{$contact->id}", $updatedData);

        $response->assertStatus(200)
            ->assertJsonFragment($updatedData);
    }

    public function test_can_delete_contact(): void
    {
        $contact = Contact::factory()->create();

        $response = $this->deleteJson("/api/v1/contacts/{$contact->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('contacts', ['id' => $contact->id]);
    }

    public function test_duplicate_email_is_rejected_within_the_current_team(): void
    {
        $team = Team::factory()->create(['user_id' => $this->authenticatedUser->id]);
        $this->authenticatedUser->forceFill(['current_team_id' => $team->id])->save();

        Contact::factory()->create([
            'team_id' => $team->id,
            'email' => 'same@example.com',
        ]);

        $this->postJson('/api/v1/contacts', [
            'name' => 'Duplicate',
            'email' => 'SAME@example.com',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }

    public function test_duplicate_email_is_allowed_in_different_teams(): void
    {
        $firstTeam = Team::factory()->create(['user_id' => $this->authenticatedUser->id]);
        $this->authenticatedUser->forceFill(['current_team_id' => $firstTeam->id])->save();
        Contact::factory()->create([
            'team_id' => $firstTeam->id,
            'email' => 'shared@example.com',
        ]);

        $secondTeam = Team::factory()->create(['user_id' => $this->authenticatedUser->id]);
        $this->authenticatedUser->forceFill(['current_team_id' => $secondTeam->id])->save();

        $this->postJson('/api/v1/contacts', [
            'name' => 'Other Team Contact',
            'email' => 'shared@example.com',
        ])->assertCreated();
    }
}
