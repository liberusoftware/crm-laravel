<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Contact;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LeadApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->withPersonalTeam()->create();
        Sanctum::actingAs($this->user);
    }

    public function test_can_list_search_filter_sort_and_paginate_leads(): void
    {
        Lead::factory()->create([
            'team_id' => $this->user->currentTeam->id,
            'status' => 'new',
            'source' => 'website',
        ]);
        Lead::factory()->create([
            'team_id' => $this->user->currentTeam->id,
            'status' => 'qualified',
            'source' => 'referral',
        ]);

        $this->getJson('/api/v1/leads?search=qualified&status=qualified&sort_by=score&sort_direction=asc&per_page=1')
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.status', 'qualified')
            ->assertJsonPath('per_page', 1);
    }

    public function test_can_create_and_score_a_lead(): void
    {
        $response = $this->postJson('/api/v1/leads', [
            'status' => 'new',
            'source' => 'referral',
            'potential_value' => 30000,
            'lifecycle_stage' => 'lead',
        ]);

        $response->assertCreated()
            ->assertJsonPath('status', 'new')
            ->assertJsonPath('score', 70);
    }

    public function test_store_rejects_a_contact_from_another_team(): void
    {
        $otherUser = User::factory()->withPersonalTeam()->create();
        $contact = Contact::factory()->create(['team_id' => $otherUser->currentTeam->id]);

        $this->postJson('/api/v1/leads', ['contact_id' => $contact->id])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['contact_id']);
    }

    public function test_can_update_and_delete_a_lead(): void
    {
        $lead = Lead::factory()->create(['team_id' => $this->user->currentTeam->id]);

        $this->putJson("/api/v1/leads/{$lead->id}", [
            'status' => 'qualified',
            'lifecycle_stage' => 'opportunity',
        ])->assertOk()
            ->assertJsonPath('status', 'qualified')
            ->assertJsonPath('lifecycle_stage', 'opportunity');

        $this->deleteJson("/api/v1/leads/{$lead->id}")->assertNoContent();
        $this->assertDatabaseMissing('leads', ['id' => $lead->id]);
    }

    public function test_cannot_access_a_lead_from_another_team(): void
    {
        $otherUser = User::factory()->withPersonalTeam()->create();
        $lead = Lead::factory()->create(['team_id' => $otherUser->currentTeam->id]);

        $this->getJson("/api/v1/leads/{$lead->id}")->assertNotFound();
    }
}
