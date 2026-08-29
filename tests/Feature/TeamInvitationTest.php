<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Jetstream\Mail\TeamInvitation;
use Tests\TestCase;

class TeamInvitationTest extends TestCase
{
    use RefreshDatabase;

    public function test_team_members_can_be_invited_to_team(): void
    {
        Mail::fake();

        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $this->post('/team-invitations', [
            'email' => 'test@example.com',
            'role' => 'admin',
            'team_id' => $user->currentTeam->id,
        ]);

        Mail::assertSent(TeamInvitation::class);

        $this->assertCount(1, $user->currentTeam->fresh()->teamInvitations);
    }

    public function test_team_member_invitations_can_be_cancelled(): void
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $invitation = $user->currentTeam->teamInvitations()->create([
            'email' => 'test@example.com',
            'role' => 'admin',
            'token' => Str::random(40),
        ]);

        $this->delete('/team-invitations/'.$invitation->id);

        $this->assertCount(0, $user->currentTeam->fresh()->teamInvitations);
    }

    public function test_invited_user_cannot_invite_another_user_before_accepting(): void
    {
        Mail::fake();

        $owner = User::factory()->withPersonalTeam()->create();
        $invitedUser = User::factory()->create();
        $team = $owner->currentTeam;
        $team->teamInvitations()->create([
            'email' => $invitedUser->email,
            'role' => 'admin',
            'token' => Str::random(40),
        ]);

        $this->actingAs($invitedUser)
            ->post('/team-invitations', [
                'email' => 'another@example.com',
                'role' => 'admin',
                'team_id' => $team->id,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('team_invitations', ['email' => 'another@example.com']);
    }

    public function test_user_cannot_accept_another_users_invitation(): void
    {
        $owner = User::factory()->withPersonalTeam()->create();
        $invitedUser = User::factory()->create();
        $attacker = User::factory()->create();
        $invitation = $owner->currentTeam->teamInvitations()->create([
            'email' => $invitedUser->email,
            'role' => 'admin',
            'token' => Str::random(40),
        ]);

        $this->actingAs($attacker)
            ->post('/team-invitations/'.$invitation->id.'/accept')
            ->assertForbidden();

        $this->assertDatabaseHas('team_invitations', ['id' => $invitation->id]);
        $this->assertDatabaseMissing('team_user', [
            'team_id' => $owner->currentTeam->id,
            'user_id' => $attacker->id,
        ]);
    }

    public function test_invited_email_address_must_be_a_valid_email(): void
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $response = $this->post('/team-invitations', [
            'email' => 'test',
            'role' => 'admin',
            'team_id' => $user->currentTeam->id,
        ]);

        $response->assertSessionHasErrors(['email']);

        $this->assertCount(0, $user->currentTeam->fresh()->teamInvitations);
    }

    public function test_team_member_can_accept_the_invitation(): void
    {
        $team = Team::factory()->create();

        $invitedUser = User::factory()->create();

        $invitation = $team->teamInvitations()->create([
            'email' => $invitedUser->email,
            'role' => 'admin',
            'token' => Str::random(40),
        ]);

        $this->actingAs($invitedUser)->post('/team-invitations/'.$invitation->id.'/accept');

        $this->assertCount(1, $team->fresh()->users);

        $this->assertEquals($invitedUser->id, $team->fresh()->users->first()->id);

        setPermissionsTeamId($team->getKey());
        $invitedUser->unsetRelation('roles');
        $this->assertTrue($invitedUser->fresh()->hasRole('sales_rep'));
        setPermissionsTeamId(null);
    }
}
