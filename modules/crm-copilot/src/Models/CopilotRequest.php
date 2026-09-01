<?php

declare(strict_types=1);

namespace Liberu\CRM\Copilot\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $team_id
 * @property int $user_id
 * @property string $kind
 * @property string $status
 */
final class CopilotRequest extends Model
{
    use IsTenantModel;

    protected $table = 'crm_copilot_requests';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['context' => 'array', 'result' => 'array'];
    }
}
