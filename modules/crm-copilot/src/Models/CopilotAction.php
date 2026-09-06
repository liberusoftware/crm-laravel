<?php

declare(strict_types=1);

namespace Liberu\CRM\Copilot\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $team_id
 * @property int $request_id
 * @property int $user_id
 * @property string $status
 */
final class CopilotAction extends Model
{
    use IsTenantModel;

    protected $table = 'crm_copilot_actions';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['payload' => 'array', 'confirmed_at' => 'datetime', 'completed_at' => 'datetime'];
    }
}
