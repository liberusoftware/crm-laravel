<?php

declare(strict_types=1);

namespace Liberu\CRM\CaseManagement\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property int $case_id @property string $event */
final class CaseAudit extends Model
{
    protected $table = 'crm_case_audits';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['before' => 'array', 'after' => 'array'];
    }
}
