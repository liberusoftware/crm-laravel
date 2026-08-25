<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadCapture\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** @property int $team_id @property string $channel @property string $status */
final class CapturedLead extends Model
{
    protected $table = 'crm_lead_capture_leads';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['source_metadata' => 'array', 'payload' => 'array'];
    }

    public function events(): HasMany
    {
        return $this->hasMany(CaptureEvent::class, 'lead_id');
    }
}
