<?php

declare(strict_types=1);

namespace Liberu\CRM\Telephony\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $version
 * @property string|null $account_sid
 * @property string|null $auth_token
 * @property string|null $default_from_number
 */
final class TelephonySettings extends Model
{
    protected $table = 'crm_telephony_settings';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['business_hours' => 'array', 'ivr' => 'array', 'skills' => 'array', 'auth_token' => 'encrypted', 'credentials_verified_at' => 'datetime'];
    }
}
