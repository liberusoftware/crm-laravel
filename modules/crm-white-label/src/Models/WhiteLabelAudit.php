<?php

declare(strict_types=1);

namespace Liberu\CRM\WhiteLabel\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

final class WhiteLabelAudit extends Model
{
    use IsTenantModel;

    protected $table = 'crm_white_label_audits';

    protected $fillable = ['team_id', 'actor_id', 'event', 'changes', 'request_id'];

    protected function casts(): array
    {
        return ['changes' => 'array'];
    }
}
