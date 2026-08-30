<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadQualification\Models;

use Illuminate\Database\Eloquent\Model;

final class QualificationRule extends Model
{
    protected $table = 'crm_lead_qualification_rules';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['fit_threshold' => 'integer', 'engagement_threshold' => 'integer', 'framework' => 'array', 'active' => 'boolean'];
    }
}
