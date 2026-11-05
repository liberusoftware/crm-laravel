<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Enums\Role;
use App\Filament\Admin\Resources\RoleResource;
use App\Filament\Admin\Resources\RoleResource\Pages\EditRole;
use App\Filament\Admin\Resources\RoleResource\Pages\ListRoles;
use App\Filament\App\Resources\TeamRoleLogResource\Pages\ListTeamRoleLogs;
use App\Filament\App\Resources\TeamRoleResource;
use App\Filament\App\Resources\TeamRoleResource\Pages\CreateTeamRole;
use App\Filament\App\Resources\TeamRoleResource\Pages\EditTeamRole;
use App\Models\Team;
use App\Models\User;
use App\Services\RoleConfigurationService;
use App\Services\TeamManagementService;
use App\Support\RolePermissionCatalog;
use Database\Seeders\RolesSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role as SpatieRole;
use Tests\TestCase;

class RolePermissionBuilderTest extends TestCase
{
    use RefreshDatabase;

    private function owner(): User
    {
        $this->seed(RolesSeeder::class);
        $owner = User::factory()->withPersonalTeam()->create(['email_verified_at' => now()]);
        setPermissionsTeamId($owner->current_team_id);
        $owner->assignRole('manager');
        $this->actingAs($owner);
        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($owner->currentTeam);

        return $owner;
    }

    public function test_owner_can_build_a_role_from_each_preset_without_platform_or_delete_permissions(): void
    {
        $owner = $this->owner();
        foreach (RolePermissionCatalog::presets() as $key => $label) {
            $expected = RolePermissionCatalog::preset($key);
            $this->assertNotEmpty($expected);
            Livewire::test(CreateTeamRole::class)->fillForm(['name' => $label.' specialist', 'preset' => $key])
                ->assertFormSet(['permissions' => $expected])->call('create')->assertHasNoFormErrors();
            $role = SpatieRole::query()->where('team_id', $owner->current_team_id)->where('name', $label.' specialist')->firstOrFail();
            $this->assertEqualsCanonicalizing($expected, $role->getPermissionNames()->all());
            $this->assertFalse($role->getPermissionNames()->contains(fn (string $name): bool => str_starts_with($name, 'delete_')));
        }
        $this->assertDatabaseHas('audit_logs', ['team_id' => $owner->current_team_id, 'action' => 'team.role_configured', 'user_id' => $owner->id]);
    }

    public function test_existing_role_can_apply_a_preset_and_keep_its_team_and_guard(): void
    {
        $owner = $this->owner();
        $role = SpatieRole::create(['name' => 'Custom', 'guard_name' => 'web', 'team_id' => $owner->current_team_id]);
        Livewire::test(EditTeamRole::class, ['record' => $role->id])->fillForm(['preset' => 'support'])->call('save')->assertHasNoFormErrors();
        $this->assertEqualsCanonicalizing(RolePermissionCatalog::preset('support'), $role->fresh()->getPermissionNames()->all());
        $this->assertSame($owner->current_team_id, $role->fresh()->team_id);
    }

    public function test_reserved_name_is_rejected_case_insensitively(): void
    {
        $this->owner();
        Livewire::test(CreateTeamRole::class)->fillForm(['name' => 'SUPER_ADMIN', 'permissions' => []])->call('create')->assertHasFormErrors(['name']);
        $this->assertDatabaseMissing('roles', ['name' => 'SUPER_ADMIN']);
    }

    public function test_unknown_platform_permissions_are_not_delegable(): void
    {
        $owner = $this->owner();
        Permission::create(['name' => 'platform_billing_override', 'guard_name' => 'web']);
        $this->assertArrayNotHasKey('platform_billing_override', TeamRoleResource::grantablePermissions());
        $this->expectException(ValidationException::class);
        app(RoleConfigurationService::class)->save($owner, $owner->currentTeam, ['name' => 'Escalation', 'permissions' => ['platform_billing_override']]);
    }

    public function test_team_owner_cannot_access_global_configuration_or_foreign_role(): void
    {
        $owner = $this->owner();
        $foreign = SpatieRole::create(['name' => 'Foreign', 'guard_name' => 'web', 'team_id' => Team::factory()->create()->id]);
        $this->assertFalse(RoleResource::canAccess());
        $this->assertNotContains(\BezhanSalleh\FilamentShield\Resources\Roles\RoleResource::class, Filament::getPanel('admin')->getResources());
        Livewire::test(EditTeamRole::class, ['record' => $foreign->id])->assertNotFound();
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Livewire::test(ListRoles::class)->assertForbidden();
    }

    public function test_admin_status_from_another_team_does_not_grant_management(): void
    {
        $owner = $this->owner();
        $owner->assignRole('admin');
        $other = Team::factory()->create();
        $other->users()->attach($owner);
        Filament::setTenant($other);
        $this->assertFalse(TeamRoleResource::canAccess());
    }

    public function test_super_admin_can_edit_global_role_without_touching_tenant_role_or_scope(): void
    {
        $owner = $this->owner();
        $tenantRole = SpatieRole::create(['name' => 'Local', 'guard_name' => 'web', 'team_id' => $owner->current_team_id]);
        setPermissionsTeamId(null);
        $owner->assignRole('super_admin');
        $owner->unsetRelation('roles')->unsetRelation('permissions');
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        $global = SpatieRole::query()->whereNull('team_id')->where('name', 'manager')->firstOrFail();
        Livewire::test(ListRoles::class)->assertCanSeeTableRecords([$global])->assertCanNotSeeTableRecords([$tenantRole]);
        Livewire::test(EditRole::class, ['record' => $global->id])->fillForm(['name' => 'manager', 'permissions' => ['view_contact']])
            ->call('save')->assertHasNoFormErrors();
        $this->assertEquals(['view_contact'], $global->fresh()->getPermissionNames()->all());
        $this->assertNull($global->fresh()->team_id);
        $this->assertSame(0, $tenantRole->permissions()->count());
        $this->assertTrue(DB::table('audit_logs')->whereNull('team_id')->where('action', 'platform.role_configured')->exists());
        $super = SpatieRole::query()->whereNull('team_id')->where('name', 'super_admin')->firstOrFail();
        $this->assertFalse(RoleResource::canEdit($super));
        $this->assertFalse(RoleResource::canDelete($super));
    }

    public function test_role_replacement_removes_custom_grants_only_in_the_active_team(): void
    {
        $owner = $this->owner();
        $team = $owner->currentTeam;
        $member = User::factory()->create();
        $team->users()->attach($member);
        $other = Team::factory()->create();
        $other->users()->attach($member);
        $service = app(TeamManagementService::class);
        $service->assignTeamRole($member, $other, Role::Manager);
        setPermissionsTeamId($team->id);
        $custom = app(RoleConfigurationService::class)->save($owner, $team, ['name' => 'Campaign editor', 'permissions' => ['view_campaign', 'update_campaign']]);
        $service->assignCustomRole($member, $team, $custom);
        $service->changeTeamRole($member, $team, Role::Free);
        $this->assertFalse($member->fresh()->hasRole('Campaign editor'));
        $this->assertTrue($member->fresh()->hasRole('free'));
        setPermissionsTeamId($other->id);
        $this->assertTrue($member->fresh()->hasRole('manager'));
    }

    public function test_scope_and_guard_cannot_be_overridden_and_owner_can_view_role_audit(): void
    {
        $owner = $this->owner();
        $role = app(RoleConfigurationService::class)->save($owner, $owner->currentTeam, [
            'name' => 'Scoped role', 'permissions' => ['view_contact'], 'team_id' => null, 'guard_name' => 'api',
        ]);
        $this->assertSame($owner->current_team_id, $role->team_id);
        $this->assertSame('web', $role->guard_name);
        Livewire::test(ListTeamRoleLogs::class)->assertSee('Role Scoped role configured');
    }

    public function test_assigned_role_cannot_be_deleted_without_reassigning_members(): void
    {
        $owner = $this->owner();
        $member = User::factory()->create();
        $owner->currentTeam->users()->attach($member);
        $role = app(RoleConfigurationService::class)->save($owner, $owner->currentTeam, ['name' => 'Used role', 'permissions' => ['view_contact']]);
        $member->assignRole($role);
        $this->expectException(ValidationException::class);
        app(RoleConfigurationService::class)->delete($owner, $owner->currentTeam, $role);
    }
}
