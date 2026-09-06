<?php

declare(strict_types=1);

namespace Liberu\CRM\AutomationPack\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $team_id
 * @property int $recipe_id
 * @property string $status
 */
final class AutomationApproval extends Model
{
    use IsTenantModel;

    protected $table = 'crm_automation_approvals';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['decided_at' => 'datetime'];
    }
}
