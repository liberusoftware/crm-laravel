<?php

declare(strict_types=1);

namespace Liberu\CRM\BusinessProcessManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $team_id
 * @property int $process_id
 * @property string $status
 * @property string|null $current_step
 * @property array<string,mixed>|null $context
 */
final class ProcessRun extends Model
{
    protected $table = 'crm_bpm_runs';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['context' => 'array', 'started_at' => 'datetime', 'completed_at' => 'datetime'];
    }

    public function events(): HasMany
    {
        return $this->hasMany(ProcessEvent::class, 'run_id');
    }

    public function process(): BelongsTo
    {
        return $this->belongsTo(Process::class, 'process_id');
    }
}
