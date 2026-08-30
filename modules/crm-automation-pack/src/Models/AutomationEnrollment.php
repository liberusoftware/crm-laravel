<?php

declare(strict_types=1);

namespace Liberu\CRM\AutomationPack\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $team_id
 * @property int $recipe_id
 * @property string $status
 */
final class AutomationEnrollment extends Model
{
    protected $table = 'crm_automation_enrollments';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['history' => 'array', 'enrolled_at' => 'datetime', 'completed_at' => 'datetime'];
    }
}
