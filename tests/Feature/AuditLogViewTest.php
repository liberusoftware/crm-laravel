<?php

namespace Tests\Feature;

use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Foundation\Organizations\Models\Team;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuditLogViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_audit_logs(): void
    {
        $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin = User::factory()->create();
        $admin->assignRole($role);
        $team = Team::factory()->create(['user_id' => $admin->id]);
        $admin->forceFill(['current_team_id' => $team->id])->save();

        $response = $this->actingAs($admin)->get(Filament::getPanel('admin')->getUrl($team).'/audit-logs');

        $response->assertSuccessful();
    }

    public function test_non_admin_cannot_view_audit_logs(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $user->id]);
        $user->forceFill(['current_team_id' => $team->id])->save();

        $response = $this->actingAs($user)->get(Filament::getPanel('admin')->getUrl($team).'/audit-logs');

        $response->assertForbidden();
    }
}
