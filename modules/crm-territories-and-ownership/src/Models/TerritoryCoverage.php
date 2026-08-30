<?php

declare(strict_types=1);

namespace Liberu\CRM\TerritoriesAndOwnership\Models;

use Illuminate\Database\Eloquent\Model;

final class TerritoryCoverage extends Model
{
    protected $table = 'crm_territory_coverage';

    protected $fillable = ['team_id', 'rule_id', 'covered_user_id', 'substitute_user_id', 'starts_at', 'ends_at'];

    protected function casts(): array
    {
        return ['starts_at' => 'datetime', 'ends_at' => 'datetime'];
    }
}
