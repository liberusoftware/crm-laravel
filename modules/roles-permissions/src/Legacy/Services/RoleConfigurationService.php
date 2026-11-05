<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\Role;
use App\Models\Team;
use App\Models\User;
use App\Support\RolePermissionCatalog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role as SpatieRole;
use Spatie\Permission\PermissionRegistrar;

final class RoleConfigurationService
{
    public function canManageTeam(User $actor, Team $team): bool
    {
        if ($actor->isSuperAdmin() || $actor->ownsTeam($team)) {
            return true;
        }
        if (! $actor->belongsToTeam($team)) {
            return false;
        }

        return DB::table('model_has_roles')->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('model_has_roles.model_type', $actor->getMorphClass())->where('model_has_roles.model_id', $actor->getKey())
            ->where('model_has_roles.team_id', $team->getKey())->whereNull('roles.team_id')
            ->where('roles.guard_name', 'web')->where('roles.name', Role::Admin->value)->exists();
    }

    public function save(User $actor, ?Team $team, array $input, ?SpatieRole $role = null): SpatieRole
    {
        $this->authorize($actor, $team, $role);
        $input['name'] = is_string($input['name'] ?? null) ? trim($input['name']) : ($input['name'] ?? null);
        $data = Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'permissions' => ['present', 'array'],
            'permissions.*' => ['string', 'distinct', Rule::in(array_keys(RolePermissionCatalog::options($team === null)))],
        ])->validate();
        if ($role !== null && in_array($role->name, Role::values(), true) && $data['name'] !== $role->name) {
            throw ValidationException::withMessages(['name' => 'System role names cannot be changed.']);
        }
        $reserved = in_array(mb_strtolower(str_replace([' ', '-'], '_', $data['name'])), Role::values(), true);
        if ($reserved && ($role === null || $role->name !== $data['name'] || $team !== null)) {
            throw ValidationException::withMessages(['name' => 'Choose a name that is different from a system role.']);
        }

        return DB::transaction(function () use ($actor, $team, $data, $role): SpatieRole {
            if ($team !== null) {
                Team::query()->whereKey($team->getKey())->lockForUpdate()->firstOrFail();
            } else {
                SpatieRole::query()->whereNull('team_id')->where('guard_name', 'web')->where('name', Role::SuperAdmin->value)->lockForUpdate()->firstOrFail();
            }
            if (SpatieRole::query()->where('guard_name', 'web')->where('team_id', $team?->getKey())
                ->whereRaw('LOWER(name) = ?', [mb_strtolower($data['name'])])->when($role, fn ($query) => $query->whereKeyNot($role->getKey()))->exists()) {
                throw ValidationException::withMessages(['name' => 'A role with this name already exists in this scope.']);
            }
            if ($role !== null) {
                $role = SpatieRole::query()->whereKey($role->getKey())->lockForUpdate()->firstOrFail();
            }
            $this->authorize($actor, $team, $role);
            $before = $role?->getPermissionNames()->sort()->values()->all() ?? [];
            $role ??= new SpatieRole();
            $role->fill(['name' => $data['name'], 'guard_name' => 'web', 'team_id' => $team?->getKey()]);
            $role->save();
            $role->syncPermissions($data['permissions']);
            $this->audit($actor, $team, $role, 'configured', ['before' => $before, 'after' => $data['permissions']]);
            app(PermissionRegistrar::class)->forgetCachedPermissions();
            $actor->unsetRelation('roles')->unsetRelation('permissions');

            return $role;
        });
    }

    public function delete(User $actor, ?Team $team, SpatieRole $role): void
    {
        $this->authorize($actor, $team, $role);
        abort_if(in_array($role->name, Role::values(), true), 403, 'System roles cannot be deleted.');
        DB::transaction(function () use ($actor, $team, $role): void {
            SpatieRole::query()->whereKey($role->id)->lockForUpdate()->firstOrFail();
            if (DB::table('model_has_roles')->where('role_id', $role->id)->exists()) {
                throw ValidationException::withMessages(['role' => 'Reassign members before deleting this role.']);
            }
            $this->audit($actor, $team, $role, 'deleted', ['permissions' => $role->getPermissionNames()->all()]);
            $role->delete();
            app(PermissionRegistrar::class)->forgetCachedPermissions();
        });
    }

    private function authorize(User $actor, ?Team $team, ?SpatieRole $role): void
    {
        abort_unless($team === null ? $actor->isSuperAdmin() : $this->canManageTeam($actor, $team), 403);
        if ($role !== null) {
            abort_unless($role->guard_name === 'web' && ($team === null ? $role->getAttribute('team_id') === null : (int) $role->getAttribute('team_id') === (int) $team->getKey()), 403);
            abort_if(mb_strtolower($role->name) === Role::SuperAdmin->value, 403, 'The platform super administrator role is protected.');
        }
    }

    private function audit(User $actor, ?Team $team, SpatieRole $role, string $event, array $changes): void
    {
        // Preserve a null platform scope instead of invoking the model's tenant-stamping hook.
        DB::table('audit_logs')->insert([
            'user_id' => $actor->getKey(), 'team_id' => $team?->getKey(),
            'action' => ($team === null ? 'platform.role_' : 'team.role_').$event,
            'description' => 'Role '.$role->name.' '.$event,
            'auditable_type' => $role->getMorphClass(), 'auditable_id' => $role->getKey(),
            'changes' => json_encode($changes, JSON_THROW_ON_ERROR),
            'ip_address' => request()->ip() ?? '0.0.0.0', 'created_at' => now(), 'updated_at' => now(),
        ]);
    }
}
