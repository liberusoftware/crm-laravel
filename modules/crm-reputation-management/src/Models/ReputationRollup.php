<?php

declare(strict_types=1);

namespace Liberu\CRM\ReputationManagement\Models;

use Illuminate\Database\Eloquent\Model;

final class ReputationRollup extends Model
{
    protected $table = 'crm_reputation_rollups';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['review_count' => 'integer', 'average_rating' => 'float', 'sentiment' => 'array', 'period_start' => 'date', 'period_end' => 'date'];
    }
}
