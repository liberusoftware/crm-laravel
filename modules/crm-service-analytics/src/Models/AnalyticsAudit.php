<?php

declare(strict_types=1);

namespace Liberu\CRM\ServiceAnalytics\Models;

use Illuminate\Database\Eloquent\Model;

final class AnalyticsAudit extends Model
{
    protected $table = 'crm_service_analytics_audits';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['details' => 'array'];
    }
}
