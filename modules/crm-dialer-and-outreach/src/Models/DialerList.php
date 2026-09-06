<?php

declare(strict_types=1);

namespace Liberu\CRM\DialerAndOutreach\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property string $mode @property string $status */
final class DialerList extends Model
{
    use IsTenantModel;

    protected $table = 'crm_dialer_lists';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['local_time_policy' => 'array', 'compliance' => 'array', 'script' => 'array'];
    }
}
