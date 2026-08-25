<?php

declare(strict_types=1);

namespace Liberu\CRM\Telephony\Models;

use Illuminate\Database\Eloquent\Model;

final class TelephonyQueue extends Model
{
    protected $table = 'crm_telephony_queues';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['members' => 'array', 'active' => 'boolean'];
    }
}
