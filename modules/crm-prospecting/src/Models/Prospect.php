<?php

declare(strict_types=1);

namespace Liberu\CRM\Prospecting\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $team_id
 */
final class Prospect extends Model
{
    use IsTenantModel;

    protected $table = 'crm_prospects';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['provenance' => 'array', 'metadata' => 'array'];
    }
}
