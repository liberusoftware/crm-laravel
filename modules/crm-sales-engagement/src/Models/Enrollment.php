<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesEngagement\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $status
 * @property int $current_step
 * @property int $reentry_count
 */
final class Enrollment extends Model
{
    protected $table = 'crm_engagement_enrollments';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['next_run_at' => 'datetime'];
    }
}
