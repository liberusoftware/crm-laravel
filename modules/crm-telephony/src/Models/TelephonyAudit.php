<?php

declare(strict_types=1);

namespace Liberu\CRM\Telephony\Models;

use Illuminate\Database\Eloquent\Model;

final class TelephonyAudit extends Model
{
    protected $table = 'crm_telephony_audits';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['details' => 'array'];
    }
}
