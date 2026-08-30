<?php

declare(strict_types=1);

namespace Liberu\CRM\WebIntent\Models;

use Illuminate\Database\Eloquent\Model;

final class WebIntentEngagement extends Model
{
    protected $table = 'crm_web_intent_engagements';

    protected $fillable = ['team_id', 'visit_id', 'visitor_key', 'event_type', 'page_url', 'content_type', 'content_id', 'points', 'duration_seconds', 'dedupe_key', 'metadata', 'occurred_at'];

    protected function casts(): array
    {
        return ['points' => 'integer', 'duration_seconds' => 'integer', 'metadata' => 'array', 'occurred_at' => 'datetime'];
    }
}
