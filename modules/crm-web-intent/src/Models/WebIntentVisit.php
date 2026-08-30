<?php

declare(strict_types=1);

namespace Liberu\CRM\WebIntent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $team_id
 * @property string $visitor_key
 * @property int $score
 * @property string $intent_level
 * @property string $consent_status
 */
final class WebIntentVisit extends Model
{
    protected $table = 'crm_web_intent_visits';

    protected $fillable = ['team_id', 'visitor_key', 'session_key', 'ip_hash', 'user_agent_hash', 'landing_url', 'referrer', 'consent_status', 'score', 'intent_level', 'status', 'started_at', 'ended_at', 'metadata'];

    protected function casts(): array
    {
        return ['score' => 'integer', 'metadata' => 'array', 'started_at' => 'datetime', 'ended_at' => 'datetime'];
    }

    /** @return HasMany<WebIntentEngagement, $this> */
    public function engagements(): HasMany
    {
        return $this->hasMany(WebIntentEngagement::class, 'visit_id');
    }
}
