<?php

declare(strict_types=1);

namespace Liberu\CRM\AutomationPack\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $team_id
 * @property int $recipe_id
 * @property string $status
 */
final class AutomationRun extends Model
{
    protected $table = 'crm_automation_runs';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['input' => 'array', 'output' => 'array'];
    }
}
