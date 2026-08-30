<?php

declare(strict_types=1);

namespace Liberu\CRM\ConsentAndPreferences\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $team_id
 * @property string $status
 * @property string $lawful_basis
 * @property string $subject_type
 * @property int $subject_id
 * @property string $channel
 * @property string $topic
 * @property Carbon|null $expires_at
 * @property Carbon|null $withdrawn_at
 */
final class ConsentRecord extends Model
{
    protected $table = 'crm_consent_records';

    protected $fillable = ['team_id', 'subject_type', 'subject_id', 'channel', 'topic', 'lawful_basis', 'status', 'source', 'proof', 'consented_at', 'expires_at', 'withdrawn_at', 'actor_id'];

    protected function casts(): array
    {
        return ['proof' => 'array', 'consented_at' => 'datetime', 'expires_at' => 'datetime', 'withdrawn_at' => 'datetime'];
    }

    public function isActive(): bool
    {
        return $this->status === 'granted' && $this->withdrawn_at === null && ($this->expires_at === null || $this->expires_at->isFuture());
    }
}
