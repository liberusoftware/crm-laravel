<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Str;

it('accepts assignable invitation roles without retaining a more privileged default role', function (string $role): void {
    $team = Team::factory()->create();
    $user = User::factory()->create();
    $invitation = $team->teamInvitations()->create(['email' => $user->email, 'role' => $role, 'token' => Str::random(40)]);
    $this->actingAs($user)->post('/team-invitations/'.$invitation->id.'/accept')->assertRedirect();
    setPermissionsTeamId($team->getKey());
    expect($user->fresh()->getRoleNames()->all())->toBe([$role]);
    setPermissionsTeamId(null);
})->with(['manager', 'sales_rep', 'free']);
