<?php

declare(strict_types=1);

namespace Liberu\CRM\PartnerRelationshipManagement\Models;

use Illuminate\Database\Eloquent\Model;

final class PartnerPerformance extends Model
{
    protected $table = 'crm_partner_performance';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['revenue' => 'float', 'deals' => 'integer', 'score' => 'float', 'period_start' => 'date', 'period_end' => 'date'];
    }
}
