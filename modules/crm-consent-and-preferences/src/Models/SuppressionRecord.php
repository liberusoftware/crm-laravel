<?php

declare(strict_types=1);

namespace Liberu\CRM\ConsentAndPreferences\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property Carbon|null $expires_at
 * @property Carbon|null $withdrawn_at
 */
final class SuppressionRecord extends Model
{
    protected $table = 'crm_suppression_records';

    protected $fillable = ['team_id', 'subject_type', 'subject_id', 'channel', 'topic', 'reason', 'source', 'expires_at', 'withdrawn_at', 'actor_id'];

    protected function casts(): array
    {
        return ['expires_at' => 'datetime', 'withdrawn_at' => 'datetime'];
    }

    public function isActive(): bool
    {
        return $this->withdrawn_at === null && ($this->expires_at === null || $this->expires_at->isFuture());
    }
}
