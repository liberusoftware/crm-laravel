<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesEngagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $enrollment_id
 * @property int $enrollment_run
 * @property int|null $step_id
 * @property string $status
 * @property Carbon|null $due_at
 * @property array $payload
 */
final class EngagementTask extends Model
{
    protected $table = 'crm_engagement_tasks';

    protected $guarded = [];

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    protected function casts(): array
    {
        return ['enrollment_run' => 'integer', 'step_id' => 'integer', 'due_at' => 'datetime', 'completed_at' => 'datetime', 'payload' => 'array'];
    }
}
