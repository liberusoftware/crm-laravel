<?php

namespace App\Services;

use App\Enums\Role;
use App\Models\Branch;
use App\Models\Team;
use App\Models\User;
use App\Support\RolePermissionCatalog;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Spatie\Permission\Models\Role as SpatieRole;

class TeamManagementService
{
    /** Roles a team admin may assign to a member (super_admin is global, customer is portal). */
    public const TEAM_ROLES = [Role::Manager, Role::SalesRep, Role::Free];

    public function createTeamMember(Team $team, string $name, string $email, string $password, Role $role): User
    {
        $this->authorizeMemberManagement($team);
        setPermissionsTeamId($team->getKey());

        if (! in_array($role, self::TEAM_ROLES, true)) {
            throw new InvalidArgumentException('That role cannot be assigned to a new team member.');
        }

        if (User::where('email', $email)->exists()) {
            throw new InvalidArgumentException('That email address is already registered.');
        }

        if (mb_strlen($password) < 12) {
            throw new InvalidArgumentException('Passwords must contain at least 12 characters.');
        }

        return DB::transaction(function () use ($team, $name, $email, $password, $role): User {
            $user = User::create(['name' => $name, 'email' => $email, 'password' => $password]);
            $team->users()->attach($user, ['role' => 'member']);
            $this->assignTeamRole($user, $team, $role);

            app(AuditLogService::class)->record(
                'team.member_added',
                "Added {$email} to the team with role {$role->value}",
                $user,
            );

            return $user;
        });
    }

    /**
     * Add an existing user to a team with a team role. Attaches membership if
     * needed, then delegates to changeTeamRole (role guard + owner guard + audit).
     */
    public function addTeamMember(User $user, Team $team, Role $role): void
    {
        $this->authorizeMemberManagement($team);
        if (! in_array($role, self::TEAM_ROLES, true)) {
            throw new InvalidArgumentException('That role cannot be assigned to a team member.');
        }
        DB::transaction(function () use ($user, $team, $role): void {
            if (! $user->belongsToTeam($team)) {
                $team->users()->attach($user);
                $user->unsetRelation('teams');
            }
            $this->changeTeamRole($user, $team, $role);
        });
    }

    /**
     * Remove a user from a team: strips their team roles, detaches membership,
     * and audits it. The team owner cannot be removed (mirrors the role guard).
     */
    public function removeTeamMember(User $user, Team $team): void
    {
        $this->authorizeMemberManagement($team, $user);
        if ($user->getKey() === $team->getAttribute('user_id')) {
            throw new InvalidArgumentException('The team owner cannot be removed.');
        }

        setPermissionsTeamId($team->getKey());

        DB::transaction(function () use ($user, $team): void {
            $this->clearTeamRoles($user, $team);

            $team->users()->detach($user);

            app(AuditLogService::class)->record(
                'team.member_removed',
                "Removed {$user->getAttribute('email')} from the team",
                $user,
            );
        });
    }

    public function changeTeamRole(User $user, Team $team, Role $role): void
    {
        $this->authorizeMemberManagement($team, $user);
        $this->provisionTeamRole($user, $team, $role);
    }

    /** Called only after invitation or identity-provider verification, before an actor session may exist. */
    public function provisionTeamRole(User $user, Team $team, Role $role): void
    {
        if (! $team->users()->whereKey($user->getKey())->exists()) {
            throw new InvalidArgumentException('The user is not a member of this team.');
        }
        if (! in_array($role, self::TEAM_ROLES, true)) {
            throw new InvalidArgumentException("Role {$role->value} is not assignable to a team member.");
        }

        // The owner manages the team; their role is immutable here, so a team can
        // never be left without its owner as a stable admin.
        if ($user->getKey() === $team->getAttribute('user_id')) {
            throw new InvalidArgumentException("The team owner's role cannot be changed.");
        }

        setPermissionsTeamId($team->getKey());
        $definition = SpatieRole::firstOrCreate(['name' => $role->value, 'guard_name' => 'web', 'team_id' => null]);

        $previous = $user->getRoleNames()->first() ?? 'none';

        DB::transaction(function () use ($user, $team, $definition, $previous, $role): void {
            $this->clearTeamRoles($user, $team);
            $user->assignRole($definition);

            app(AuditLogService::class)->record(
                'team.role_changed',
                "Changed {$user->getAttribute('email')} from {$previous} to {$role->value}",
                $user,
            );
        });
    }

    /**
     * Assign one of this team's *custom* Spatie roles (team_id = this team) to a
     * member, replacing their current team role. Mirrors changeTeamRole's owner
     * guard + audit, but the role is a per-team custom role instead of a fixed
     * enum role. Removes any fixed team role AND any of this team's custom roles
     * the member already holds, then assigns the new one.
     */
    public function assignCustomRole(User $user, Team $team, SpatieRole $customRole): void
    {
        $this->authorizeMemberManagement($team, $user);
        // Cast: the retrieved role's team_id may come back as a string on some
        // drivers, so compare as ints — a role from another team is not assignable.
        if ((int) $customRole->getAttribute('team_id') !== (int) $team->getKey()) {
            throw new InvalidArgumentException('The role does not belong to this team.');
        }

        // The owner manages the team; their role is immutable here (mirrors changeTeamRole).
        if ($user->getKey() === $team->getAttribute('user_id')) {
            throw new InvalidArgumentException("The team owner's role cannot be changed.");
        }

        setPermissionsTeamId($team->getKey());

        $previous = $user->getRoleNames()->first() ?? 'none';

        if ($customRole->guard_name !== 'web' || in_array(mb_strtolower($customRole->name), Role::values(), true)
            || array_diff($customRole->getPermissionNames()->all(), array_keys(RolePermissionCatalog::options())) !== []) {
            throw new InvalidArgumentException('This custom role contains permissions that cannot be assigned by a team owner.');
        }
        $newRole = $customRole->getAttribute('name');
        DB::transaction(function () use ($user, $team, $customRole, $previous, $newRole): void {
            $customRole = SpatieRole::query()->whereKey($customRole->getKey())->lockForUpdate()->firstOrFail();
            $this->clearTeamRoles($user, $team);
            $user->assignRole($customRole);

            app(AuditLogService::class)->record(
                'team.role_changed',
                "Changed {$user->getAttribute('email')} from {$previous} to {$newRole}",
                $user,
            );
        });
    }

    private function authorizeMemberManagement(Team $team, ?User $member = null): void
    {
        $actor = Auth::user();
        if (! $actor instanceof User || ! app(RoleConfigurationService::class)->canManageTeam($actor, $team)) {
            throw new InvalidArgumentException('You cannot manage members for this team.');
        }
        if ($member !== null && (! $member->belongsToTeam($team) || $member->getKey() === $actor->getKey())) {
            throw new InvalidArgumentException('Choose another member of this team.');
        }
    }

    private function clearTeamRoles(User $user, Team $team): void
    {
        DB::table('model_has_roles')->where('team_id', $team->getKey())->where('model_type', $user->getMorphClass())
            ->where('model_id', $user->getKey())->delete();
        $user->unsetRelation('roles')->unsetRelation('permissions');
    }

    public function createDefaultTeamForUser(User $user): Team
    {
        try {
            $defaultBranch = Branch::firstOrFail();
        } catch (ModelNotFoundException) {
            throw new Exception('No default branch found. Please set up at least one branch.');
        }

        $team = $user->ownedTeams()->create([
            'name' => $defaultBranch->name.' Team',
            'personal_team' => false,
            'branch_id' => $defaultBranch->id,
        ]);

        $this->assignTeamRole($user, $team, Role::Admin);

        return $team;
    }

    public function createPersonalTeamForUser(User $user): Team
    {
        $team = $user->ownedTeams()->create([
            'name' => $user->name."'s Team",
            'personal_team' => true,
        ]);

        $this->assignTeamRole($user, $team, Role::Admin);

        return $team;
    }

    /**
     * Assign a Spatie role to a user scoped to a specific team.
     *
     * The role definition is global (team_id = null); the assignment carries the
     * team. Wrapping setPermissionsTeamId around assignRole is what writes the
     * per-team pivot row. firstOrCreate keeps it safe when roles are not seeded.
     */
    public function assignTeamRole(User $user, Team $team, Role $role): void
    {
        setPermissionsTeamId($team->getKey());
        SpatieRole::firstOrCreate([
            'name' => $role->value,
            'guard_name' => 'web',
            'team_id' => null,
        ]);
        $user->assignRole($role->value);
    }

    public function assignUserToDefaultTeam(User $user): void
    {
        $defaultTeam = Team::where('personal_team', false)->first();

        if (! $defaultTeam) {
            try {
                $defaultTeam = $this->createDefaultTeamForUser($user);
            } catch (\Throwable) {
                // Fallback: create a personal team when no branch/default team exists
                $defaultTeam = $this->createPersonalTeamForUser($user);
                $user->current_team_id = $defaultTeam->id;
                $user->save();

                return;
            }
        }

        $this->assignUserToTeam($user, $defaultTeam);
    }

    public function assignUserToTeam(User $user, Team $team): void
    {
        if (! $user->belongsToTeam($team)) {
            $user->teams()->attach($team, ['role' => 'member']);
            $user->unsetRelation('teams'); // drop cached (pre-attach) relation so switchTeam re-checks membership
            // New member of an existing team → sales_rep in that team. Raw
            // attach() fires no Jetstream event, so assign here.
            $this->assignTeamRole($user, $team, Role::SalesRep);
        }
        $user->switchTeam($team);
    }

    public function switchTeam(User $user, Team $team): void
    {
        if (! $user->belongsToTeam($team)) {
            throw new Exception('User does not belong to the specified team.');
        }
        $user->switchTeam($team);
    }
}
