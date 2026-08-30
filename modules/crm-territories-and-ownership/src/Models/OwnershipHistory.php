<?php

declare(strict_types=1);

namespace Liberu\CRM\TerritoriesAndOwnership\Models;

use Illuminate\Database\Eloquent\Model;

final class OwnershipHistory extends Model
{
    protected $table = 'crm_ownership_history';

    protected $fillable = ['team_id', 'subject_type', 'subject_id', 'previous_owner_id', 'owner_id', 'reason', 'actor_id'];
}
