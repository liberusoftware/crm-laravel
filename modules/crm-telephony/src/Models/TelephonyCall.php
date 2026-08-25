<?php

declare(strict_types=1);

namespace Liberu\CRM\Telephony\Models;

use Illuminate\Database\Eloquent\Model;

final class TelephonyCall extends Model
{
    protected $table = 'crm_telephony_calls';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['started_at' => 'datetime', 'ended_at' => 'datetime', 'metadata' => 'array'];
    }
}
