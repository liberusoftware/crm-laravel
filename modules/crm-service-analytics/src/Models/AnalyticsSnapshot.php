<?php

declare(strict_types=1);

namespace Liberu\CRM\ServiceAnalytics\Models;

use Illuminate\Database\Eloquent\Model;

/** @property string $metric */
final class AnalyticsSnapshot extends Model
{
    protected $table = 'crm_service_analytics_snapshots';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['period_start' => 'datetime', 'period_end' => 'datetime', 'generated_at' => 'datetime', 'dimensions' => 'array'];
    }
}
