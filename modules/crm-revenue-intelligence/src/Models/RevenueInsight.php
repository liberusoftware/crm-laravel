<?php

declare(strict_types=1);

namespace Liberu\CRM\RevenueIntelligence\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $team_id
 */
final class RevenueInsight extends Model
{
    protected $table = 'crm_revenue_insights';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['payload' => 'array', 'observed_at' => 'datetime', 'score' => 'integer'];
    }
}
