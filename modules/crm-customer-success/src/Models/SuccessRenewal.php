<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerSuccess\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property int $customer_id @property string $status */
final class SuccessRenewal extends Model
{
    use IsTenantModel;

    protected $table = 'crm_success_renewals';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['renewal_date' => 'date', 'value' => 'decimal:2', 'attribution' => 'array'];
    }
}
