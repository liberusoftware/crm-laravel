<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesEngagement\Models;

use Illuminate\Database\Eloquent\Model;

final class EngagementTask extends Model
{
    protected $table = 'crm_engagement_tasks';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['due_at' => 'datetime', 'completed_at' => 'datetime', 'payload' => 'array'];
    }
}
