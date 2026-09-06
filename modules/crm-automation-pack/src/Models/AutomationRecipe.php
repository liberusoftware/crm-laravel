<?php

declare(strict_types=1);

namespace Liberu\CRM\AutomationPack\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $team_id
 * @property string $status
 * @property int $version
 * @property bool $approval_required
 * @property array<string, mixed> $triggers
 * @property array<int, mixed> $actions
 */
final class AutomationRecipe extends Model
{
    use IsTenantModel;

    protected $table = 'crm_automation_recipes';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['triggers' => 'array', 'conditions' => 'array', 'actions' => 'array', 'approval_required' => 'boolean'];
    }
}
