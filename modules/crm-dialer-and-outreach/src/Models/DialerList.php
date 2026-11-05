<?php

declare(strict_types=1);

namespace Liberu\CRM\DialerAndOutreach\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $team_id
 * @property int|null $owner_id
 * @property string $mode
 * @property string $status
 * @property array|null $script
 * @property array|null $compliance
 * @property array|null $local_time_policy
 */
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
