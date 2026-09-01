<?php

declare(strict_types=1);

namespace Liberu\CRM\Memberships\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property string $status @property float $price */
final class MembershipPlan extends Model
{
    use IsTenantModel;

    protected $table = 'crm_membership_plans';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['price' => 'decimal:2', 'metadata' => 'array'];
    }
}
