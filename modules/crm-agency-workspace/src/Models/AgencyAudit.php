<?php

declare(strict_types=1);

namespace Liberu\CRM\AgencyWorkspace\Models;

use Illuminate\Database\Eloquent\Model;

final class AgencyAudit extends Model
{
    protected $table = 'crm_agency_audits';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['payload' => 'array'];
    }
}
