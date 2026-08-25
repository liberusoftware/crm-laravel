<?php

declare(strict_types=1);

namespace Liberu\CRM\Analytics\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $team_id @property int $asset_id @property int $actor_id @property string $status */
final class AnalyticsExecution extends Model
{
    protected $table = 'crm_analytics_executions';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['parameters' => 'array', 'result' => 'array'];
    }
}
