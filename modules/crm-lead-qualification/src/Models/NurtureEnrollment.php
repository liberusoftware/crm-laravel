<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadQualification\Models;

use Illuminate\Database\Eloquent\Model;

final class NurtureEnrollment extends Model
{
    protected $table = 'crm_lead_qualification_nurtures';

    protected $fillable = ['team_id', 'qualification_id', 'actor_id', 'status', 'sequence', 'starts_at', 'ends_at', 'metadata'];

    protected function casts(): array
    {
        return ['starts_at' => 'datetime', 'ends_at' => 'datetime', 'metadata' => 'array'];
    }
}
