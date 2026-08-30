<?php

declare(strict_types=1);

namespace Liberu\CRM\AgencyWorkspace\Models;

use Illuminate\Database\Eloquent\Model;

final class AgencyAccess extends Model
{
    protected $table = 'crm_agency_access';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['expires_at' => 'datetime'];
    }
}
