<?php

declare(strict_types=1);

namespace Liberu\CRM\TerritoriesAndOwnership\Models;

use Illuminate\Database\Eloquent\Model;

final class TerritoryRule extends Model
{
    protected $table = 'crm_territory_rules';

    protected $fillable = ['team_id', 'name', 'book_of_business', 'criteria', 'members', 'capacity', 'round_robin_cursor', 'active'];

    protected function casts(): array
    {
        return ['criteria' => 'array', 'members' => 'array', 'active' => 'boolean', 'capacity' => 'integer', 'round_robin_cursor' => 'integer'];
    }
}
