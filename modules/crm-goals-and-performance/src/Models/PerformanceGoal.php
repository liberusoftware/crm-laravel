<?php

declare(strict_types=1);

namespace Liberu\CRM\GoalsAndPerformance\Models;

use App\Traits\IsTenantModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** @property int $team_id @property int $owner_id @property float $target @property float $actual */
final class PerformanceGoal extends Model
{
    use IsTenantModel;

    protected $table = 'crm_performance_goals';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['target' => 'decimal:2', 'actual' => 'decimal:2', 'starts_on' => 'date', 'ends_on' => 'date', 'metadata' => 'array'];
    }

    public function events(): HasMany
    {
        return $this->hasMany(PerformanceEvent::class, 'goal_id');
    }
}
