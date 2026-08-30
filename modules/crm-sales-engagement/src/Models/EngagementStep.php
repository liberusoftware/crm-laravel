<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesEngagement\Models;

use Illuminate\Database\Eloquent\Model;

final class EngagementStep extends Model
{
    protected $table = 'crm_engagement_steps';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['snippet' => 'array'];
    }
}
