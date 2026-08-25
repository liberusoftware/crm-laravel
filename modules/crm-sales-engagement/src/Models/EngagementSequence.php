<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesEngagement\Models;

use Illuminate\Database\Eloquent\Model;

final class EngagementSequence extends Model
{
    protected $table = 'crm_engagement_sequences';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['throttle' => 'array', 'stop_rules' => 'array', 'experiment' => 'array'];
    }
}
