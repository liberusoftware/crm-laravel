<?php

use App\Http\Responses\RegisterResponse;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Foundation\Organizations\Models\Team;
use Liberu\Foundation\RolesPermissions\Models\Role;

uses(RefreshDatabase::class);

function seedTeamUser(bool $superAdmin): User
{
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->forceFill(['current_team_id' => $team->id])->save();

    if ($superAdmin) {
        setPermissionsTeamId($team->id);
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web', 'team_id' => $team->id]);
        $user->assignRole('super_admin');
    }

    return $user;
}

it('sends a super_admin to the admin panel after login', function () {
    $admin = seedTeamUser(superAdmin: true);

    $this->actingAs($admin)
        ->get('/dashboard')
        ->assertRedirectContains('/admin');
});

it('sends a normal user to the app panel after login', function () {
    $user = seedTeamUser(superAdmin: false);

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertRedirect(route('filament.app.pages.dashboard', ['tenant' => $user->currentTeam]));
});

it('sends a sales representative through setup after registration', function () {
    $user = seedTeamUser(superAdmin: false);
    setPermissionsTeamId($user->current_team_id);
    Role::firstOrCreate(['name' => 'sales_rep', 'guard_name' => 'web', 'team_id' => $user->current_team_id]);
    $user->assignRole('sales_rep');
    $this->actingAs($user);

    $response = (new RegisterResponse())->toResponse(request()->create('/register', 'POST'));

    expect($response->getTargetUrl())->toBe(url('/app/'.$user->current_team_id.'/setup-wizard'));
});
