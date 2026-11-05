<?php

declare(strict_types=1);

namespace Liberu\CRM\DialerAndOutreach\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $team_id
 * @property int|null $contact_id
 * @property Carbon|null $last_attempt_at
 * @property int $list_id
 * @property string $status
 * @property bool $consent_granted
 * @property int $attempts
 * @property string $phone
 * @property string|null $outcome
 * @property string|null $provider_sid
 */
final class DialerCall extends Model
{
    use IsTenantModel;

    protected $table = 'crm_dialer_calls';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['last_attempt_at' => 'datetime', 'consent_granted' => 'boolean', 'voicemail_dropped' => 'boolean', 'next_attempt_at' => 'datetime', 'completed_at' => 'datetime'];
    }
}
