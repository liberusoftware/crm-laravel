<?php

declare(strict_types=1);

namespace Liberu\CRM\SaasPackaging\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $team_id
 * @property string $status
 * @property int $plan_id
 * @property Carbon|null $cancelled_at
 */
final class SaasSubscription extends Model
{
    protected $table = 'crm_saas_subscriptions';

    protected $guarded = [];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SaasPlan::class);
    }

    protected function casts(): array
    {
        return ['trial_ends_at' => 'datetime', 'current_period_ends_at' => 'datetime', 'cancelled_at' => 'datetime'];
    }
}
