<?php

declare(strict_types=1);

namespace Liberu\CRM\Telephony\Models;

use Illuminate\Database\Eloquent\Model;

final class TelephonyNumber extends Model
{
    protected $table = 'crm_telephony_numbers';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['caller_id_enabled' => 'boolean', 'metadata' => 'array'];
    }
}
