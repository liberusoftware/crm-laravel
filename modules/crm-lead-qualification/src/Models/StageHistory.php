<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadQualification\Models;

use Illuminate\Database\Eloquent\Model;

final class StageHistory extends Model
{
    protected $table = 'crm_lead_qualification_stage_history';

    protected $fillable = ['team_id', 'qualification_id', 'actor_id', 'from_stage', 'to_stage', 'reason'];
}
