<?php

declare(strict_types=1);

namespace Liberu\CRM\DialerAndOutreach\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $team_id
 * @property int $list_id
 * @property string $status
 * @property int $attempts
 */
final class DialerCall extends Model
{
    use IsTenantModel;

    protected $table = 'crm_dialer_calls';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['voicemail_dropped' => 'boolean', 'next_attempt_at' => 'datetime', 'completed_at' => 'datetime'];
    }
}
