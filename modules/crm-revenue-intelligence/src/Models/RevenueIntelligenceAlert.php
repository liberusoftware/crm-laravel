<?php

declare(strict_types=1);

namespace Liberu\CRM\RevenueIntelligence\Models;

use Illuminate\Database\Eloquent\Model;

final class RevenueIntelligenceAlert extends Model
{
    protected $table = 'crm_revenue_intelligence_alerts';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['payload' => 'array', 'resolved_at' => 'datetime'];
    }
}
