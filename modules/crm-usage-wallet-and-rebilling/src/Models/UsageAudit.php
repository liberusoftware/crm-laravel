<?php

declare(strict_types=1);

namespace Liberu\CRM\UsageWalletAndRebilling\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

final class UsageAudit extends Model
{
    use IsTenantModel;

    protected $table = 'crm_usage_audits';

    protected $fillable = ['team_id', 'actor_id', 'event', 'details', 'request_id'];

    protected function casts(): array
    {
        return ['details' => 'array'];
    }
}
