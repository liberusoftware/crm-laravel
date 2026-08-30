<?php

declare(strict_types=1);

namespace Liberu\CRM\Prospecting\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $team_id */
final class ProspectCredit extends Model
{
    protected $table = 'crm_prospect_credits';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['revealed_at' => 'datetime'];
    }
}
