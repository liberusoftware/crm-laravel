<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesEngagement\Models;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $team_id
 * @property string $status
 * @property int $current_step
 * @property int $reentry_count
 * @property int $sequence_id
 * @property Carbon|null $next_run_at
 */
final class Enrollment extends Model
{
    protected $table = 'crm_engagement_enrollments';

    protected $guarded = [];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    protected function casts(): array
    {
        return ['current_step' => 'integer', 'reentry_count' => 'integer', 'next_run_at' => 'datetime'];
    }
}
