<?php

declare(strict_types=1);

namespace Liberu\CRM\DialerAndOutreach\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property int $call_id @property string $event */
final class DialerEvent extends Model
{
    use IsTenantModel;

    protected $table = 'crm_dialer_events';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['metadata' => 'array', 'occurred_at' => 'datetime'];
    }
}
