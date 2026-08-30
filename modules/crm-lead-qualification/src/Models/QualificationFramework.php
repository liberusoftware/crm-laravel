<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadQualification\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $team_id
 * @property int $mql_threshold
 * @property int $pql_threshold
 * @property int $sql_threshold
 * @property int $service_qualified_threshold
 */
final class QualificationFramework extends Model
{
    protected $table = 'crm_lead_qualification_frameworks';

    protected $fillable = ['team_id', 'actor_id', 'name', 'status', 'mql_threshold', 'pql_threshold', 'sql_threshold', 'service_qualified_threshold', 'rules', 'settings'];

    protected function casts(): array
    {
        return ['rules' => 'array', 'settings' => 'array', 'mql_threshold' => 'integer', 'pql_threshold' => 'integer', 'sql_threshold' => 'integer', 'service_qualified_threshold' => 'integer'];
    }
}
