<?php

declare(strict_types=1);

namespace Liberu\CRM\Loyalty\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property string $status */
final class LoyaltyProgram extends Model
{
    use IsTenantModel;

    protected $table = 'crm_loyalty_programs';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['tiers' => 'array', 'metadata' => 'array'];
    }
}
