<?php

declare(strict_types=1);

namespace App\Services\Marketing;

use App\Models\Team;
use App\Models\User;

final class WorkspaceAccess
{
    public function canManage(User $user, Team $team): bool
    {
        return (int) $team->getAttribute('user_id') === (int) $user->getKey()
            || $team->users()->whereKey($user->getKey())->wherePivotIn('role', ['admin', 'manager'])->exists();
    }

    public function authorize(User $user, Team $team, string $permission): void
    {
        abort_unless($user->belongsToTeam($team), 403);
        abort_unless($this->canManage($user, $team) || $user->getAllPermissions()->contains('name', $permission), 403);
    }
}
