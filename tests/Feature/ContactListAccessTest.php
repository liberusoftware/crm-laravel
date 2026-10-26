<?php

use App\Models\Contact;
use App\Models\User;
use App\Support\TenantContext;

it('does not expose contact details to guests through browser listing routes', function () {
    $owner = User::factory()->withPersonalTeam()->create();
    $contact = Contact::factory()->create([
        'team_id' => $owner->current_team_id,
        'name' => 'Private Contact',
        'email' => 'private-contact@example.test',
    ]);
    auth()->forgetGuards();
    TenantContext::clear();

    foreach ([
        route('contacts.list'),
        route('contacts.list', ['created_at' => '2026-01-01']),
        route('contacts.autocomplete', ['query' => 'Private']),
    ] as $url) {
        $this->get($url)
            ->assertRedirect(route('login'))
            ->assertDontSee($contact->name)
            ->assertDontSee($contact->email);
    }
});

it('rejects contact deletion when the authenticated user has no current team', function () {
    $owner = User::factory()->withPersonalTeam()->create();
    $contact = Contact::factory()->create(['team_id' => $owner->current_team_id]);
    $user = User::factory()->create();
    TenantContext::clear();

    $this->actingAs($user)
        ->deleteJson(route('contacts.bulk.delete'), ['ids' => [$contact->id]])
        ->assertForbidden();

    $this->assertModelExists($contact);
});

it('deletes only the authenticated users team contacts without ambient tenant context', function () {
    $owner = User::factory()->withPersonalTeam()->create();
    $otherOwner = User::factory()->withPersonalTeam()->create();
    $ownContact = Contact::factory()->create(['team_id' => $owner->current_team_id]);
    $otherContact = Contact::factory()->create(['team_id' => $otherOwner->current_team_id]);
    TenantContext::clear();

    $this->actingAs($owner)
        ->deleteJson(route('contacts.bulk.delete'), ['ids' => [$ownContact->id, $otherContact->id]])
        ->assertOk()
        ->assertJsonPath('deleted', 1);

    $this->assertModelMissing($ownContact);
    $this->assertModelExists($otherContact);
});
