<?php

declare(strict_types=1);

namespace Liberu\CRM\CPQ\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $team_id
 * @property int $quote_id
 * @property int $actor_id
 * @property string $status
 */
final class CpqApproval extends Model
{
    use IsTenantModel;

    protected $table = 'crm_cpq_approvals';

    protected $guarded = [];
}
