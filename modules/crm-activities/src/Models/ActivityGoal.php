<?php

declare(strict_types=1);

namespace Liberu\CRM\Activities\Models;

use Illuminate\Database\Eloquent\Model;

final class ActivityGoal extends Model
{
    protected $table = 'crm_activity_goals';

    protected $fillable = ['team_id', 'owner_id', 'name', 'kind', 'target', 'progress', 'starts_at', 'ends_at', 'status', 'criteria'];

    protected function casts(): array
    {
        return ['target' => 'integer', 'progress' => 'integer', 'starts_at' => 'datetime', 'ends_at' => 'datetime', 'criteria' => 'array'];
    }
}
