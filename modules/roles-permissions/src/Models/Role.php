<?php

declare(strict_types=1);

namespace Liberu\Foundation\RolesPermissions\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Foundation\Organizations\Models\Team;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
}
