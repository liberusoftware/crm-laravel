<?php

declare(strict_types=1);

namespace Liberu\CRM\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

final class TimelineEntry extends Model
{
    protected $table = 'crm_core_timeline';

    protected $fillable = ['team_id', 'actor_id', 'event_type', 'summary', 'payload'];

    protected function casts(): array
    {
        return ['payload' => 'array'];
    }

    public function recordable(): MorphTo
    {
        return $this->morphTo();
    }
}
