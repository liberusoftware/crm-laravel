<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadCapture\Models;

use Illuminate\Database\Eloquent\Model;

/** @property int $team_id */
final class LeadCapture extends Model
{
    protected $table = 'crm_lead_captures';

    protected $fillable = ['team_id', 'actor_id', 'kind', 'status', 'name', 'email', 'phone', 'source', 'source_medium', 'source_campaign', 'external_id', 'dedupe_key', 'source_metadata', 'payload', 'provenance', 'captured_at', 'processed_at', 'failure_reason'];

    protected function casts(): array
    {
        return ['source_metadata' => 'array', 'payload' => 'array', 'provenance' => 'array', 'captured_at' => 'datetime', 'processed_at' => 'datetime'];
    }
}
